<?php


use App\Models\Applications;
use App\Models\ClientTypes;
use App\Models\Crime;
use App\Models\Currency;
use App\Models\Customers;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ProductDocuments;
use App\Models\Products;
use App\Models\Professions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

function checkBalance($application_id)
{
    $application = Applications::find($application_id);
    $totalLoanAmount = $application->principal_amount + $application->total_interest_amount;
    $totalPayments = Payment::where('loan_application_id', $application_id)->sum('amount');
    $balance = $totalLoanAmount - $totalPayments;
    return $balance;
}

function uploadToBucket(Request $request, $type, $idNumber)
{
    $file = $request->file($type);
    $fileName = $idNumber . '_' . $type . '.' . $file->getClientOriginalExtension();
    $payload = [
        'file' => $file,
        'bucket' => env('BUCKET'),
        'fileName' => $fileName
    ];
    filePostRequest('storage/upload', $payload);

    return $fileName;
}

function uploadDocumentToBucket($application_id, $file, $name)
{

    $fileName = $application_id . '_' . $name . '.' . $file->getClientOriginalExtension();
    $payload = [
        'file' => $file,
        'bucket' => env('BUCKET'),
        'fileName' => $fileName
    ];
    filePostRequest('storage/upload', $payload);

    return $fileName;
}


function calculateInstallment($principal, $interestRate, $tenure): array
{

    $interestAmount = ($principal * $interestRate) / 100;

    $monthlyInstallment = ($principal + $interestAmount) / $tenure;

    $monthlyInterestAmount = $interestAmount / $tenure;

    return [$monthlyInterestAmount, $monthlyInstallment, $interestAmount];

}


//function getOutstandingInstallmentsAndPrincipal($loan)
//{
//    $outstandingInstallmentsCount = $loan->outstandingInstallments()->count();
//    $outstandingInstallments = $loan->outstandingInstallments()->sum('amount');
//    $total_payments=$loan->payments()->sum('amount');
//
//    $principal_percentage = $loan->principal_amount /($loan->installment_amount * $loan->loan_tenure);
//    $principal_paid = $principal_percentage * $total_payments;
//    $outstandingPrincipal = $loan->principal_amount - $principal_paid;
//    return ['outstandingInstallments' => $outstandingInstallments, 'outstandingPrincipal' => $outstandingPrincipal, 'outstandingInstallmentsCount' => $outstandingInstallmentsCount];
//}


function respondWithToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
        'payment_methods' => PaymentMethod::all(),
        'currencies' => Currency::all(),
        'crimes' => Crime::with('category')->get(),
    ]);
}





function calculateNewRestructuredInstallment($loan, $principle_top_up = 0, $new_tenure = null, $interest = null)
{

    $loanData = getOutstandingInstallmentsAndPrincipal($loan);


    $interest = $interest ?? $loan->monthly_interest_rate;
    $interest /= 100;
    $outstanding_installments = $loanData['outstandingInstallments']; // (PENDING + PARTIAL PAYMENT)
    $outstanding_principle = $loanData['outstandingPrincipal'];
    $tenure = $new_tenure ?? $loanData['outstandingInstallmentsCount'];

    //Calculate outstanding interest
    $outstanding_interest = $outstanding_installments - $outstanding_principle;
    $new_principle = $outstanding_principle + $principle_top_up;
    $calculated_interest = $interest * $new_principle;

    if ($principle_top_up == 0) {
        $total_new_interest = $loan->monthly_interest_amount;
    } else {
        $total_new_interest = $calculated_interest + ($outstanding_interest / $tenure);
    }

    return $total_new_interest + ($new_principle / $tenure);
}

function loadProduct($id)
{
    return Products::findOrFail($id);
}

function getCustomer($phone)
{
    return Customers::where('phone_number', $phone)->first();
}

function shouldHaveECNumber($id)
{
    $clientType = ClientTypes::find($id)->name;

    return strcmp(strtolower($clientType), 'ssb') == 0;
}


function resolveAccountBalance($account)
{
    $deposits = $account->approvedDeposits()->sum('credit');
    $expenses = $account->purchases()->sum('amount');
    return $deposits - $expenses;
}

function directory()
{
    return storage_path();
}

function isDateTwoMonthsOld($date)
{
    // Convert the input date to a Carbon instance
    $inputDate = Carbon::parse($date);

    // Get the date two months ago from now
    $twoMonthsAgo = Carbon::now()->subMonths(2);

    // Check if the input date is less than or equal to two months ago
    return $inputDate <= $twoMonthsAgo;
}


function getSystemPermissions()
{
    return [

        'Access Clients',
        'Access Settings',
        'Access Users',
        'Access Applications',
        'Access Contracts',
        'Access Payments',
        'Access Reports',

        'View Clients',
        'Add Clients',
        'Edit Clients',
        'Delete Clients',
        'Manage Clients',

        'View Applications',
        'Add Applications',
        'Edit Applications',
        'Delete Applications',
        'Manage Applications',

        'View Contracts',
        'Add Contracts',
        'Edit Contracts',
        'Delete Contracts',
        'Manage Contracts',

        'View Payments',
        'Add Payments',
        'Edit Payments',
        'Delete Payments',
        'Manage Payments',

        'View Reports',
        'Add Reports',
        'Edit Reports',
        'Delete Reports',
        'Manage Reports',

        'View Settings',
        'Add Settings',
        'Edit Settings',
        'Delete Settings',
        'Manage Settings',

        'View Users',
        'Add Users',
        'Edit Users',
        'Delete Users',
        'Manage Users',


    ];
}


function outstandingPayments($customerId)
{
    return Installment::whereHas('loanApplication', function ($query) use ($customerId) {
        $query->where('customer_id', $customerId)->whereNot('status', 'COMPLETE');
    })->whereNot('status', 'PAID')->where('due_date', '<=', Carbon::today())->get();
}

function resolveOutstandingPayments($installments)
{
    $total = 0;
    foreach ($installments as $installment) {
        $total += resolveInstallmentBalance($installment);
    }
    return $total;
}

function resolveInstallmentBalance($installment)
{

    $payments = $installment->payments->sum("amount");
    $amount = $installment->amount;
    $balance = $amount - $payments;
    return $balance;
}

function getLoanBalance($loanApplicationId)
{
    // Fetch the total installments for the given loan application ID
    $result = DB::table('loan_applications as la')
        ->join('installments as i', 'la.id', '=', 'i.loan_application_id')
        ->select(DB::raw('SUM(i.amount) as total_installments'))
        ->where('la.id', $loanApplicationId)
        ->where('la.status', '!=', 'COMPLETE')
        ->groupBy('la.id')
        ->first();

    // Return the total installments or 0 if no result is found
    return $result ? $result->total_installments : 0;
}

function calculateCurrentLoanBalance($loanApplicationId, $maxDate)
{
    // Query the database to sum the total installments paid before the given max date
    $totalInstallments = DB::table('installments')
        ->where('loan_application_id', $loanApplicationId)
        ->where('start_date', '<', $maxDate)
        ->sum('amount');

    return $totalInstallments;
}

function newGetOutstandingPrincipal($application)
{

    return (1 - ($application->monthly_interest_amount / $application->installment_amount)) * getLoanBalance($application->id);
}

function resolveTotalPayments($loan)
{
    $payments = $loan->payments()->sum("amount");
    $archivedPayments = $loan->archivedPayments()->sum("amount");
    return abs($payments) + $archivedPayments;

}

function getMonthlyRestructuredInstallment($application, $principal_top_up)
{


}

function resolveProfessionsData()
{
    $professions = Professions::all();
    $formattedProfessions = $professions->map(function ($profession) {
        return "{$profession->id}.{$profession->name}";
    });

    return $formattedProfessions->implode("\n");
}


function resolveProductsData()
{
    $products = Products::all();
    $formattedProducts = $products->map(function ($product) {
        return "{$product->id}.{$product->product_name} ({$product->interest_rate}%)";
    });

    return $formattedProducts->implode("\n");
}

function allowedIds($model, $id)
{
    $namespace = 'App\Models\\' . $model;
    $allowedIds = $namespace::pluck('id')->toArray();
    return in_array($id, $allowedIds);
}

function resolveClientData($data)
{
    $data = json_decode($data, true);

    return "
    First Name: {$data['first_name']}\n
    Last Name: {$data['last_name']}\n
    National ID: {$data['national_id_no']}\n
    Date of Birth: {$data['dob']}\n
    Gender: {$data['gender']}\n
    Address: {$data['address']}\n
    City: {$data['city']}\n
    Email: {$data['email']}\n
    Phone: {$data['phone_number']}\n
    1. Confirm the information is correct
    2. Cancel
";


}

function resolveLoanData($data)
{

//   $data = json_decode($data, true);

    return "
    Customer Name: {$data['customer']['first_name']} {$data['customer']['last_name']}\n
    National ID: {$data['customer']['national_id_no']}\n
    Date of Birth: {$data['customer']['dob']}\n
    Gender: {$data['customer']['gender']}\n
    Address: {$data['customer']['address']}\n
    City: {$data['customer']['city']}\n
    Email: {$data['customer']['email']}\n
    Phone: {$data['customer']['phone_number']}\n
    Product: {$data['product']['product_name']}\n
    Monthly Interest Rate: {$data['interest_rate']}%\n
    Monthly Interest Amount: {$data['monthly_interest_amount']}\n
    Total Interest Amount: {$data['total_interest_amount']}\n
    Monthly Installment: {$data['monthly_installment']}\n
    Total Repayment: {$data['total_repayment']}\n
    Principal Amount: {$data['principal_amount']}\n
    Loan Tenure: {$data['loan_tenure']} months\n

    1. Confirm the information is correct\n
    2. Cancel
";
}


function removeWhatsAppPrefix($phoneNumber)
{
    return str_replace('whatsapp:', '', $phoneNumber);
}

function resolveLoanFinalisationInformation($application, $isBot = null)
{

    $application = (object)$application;
    $monthly_interest_amount = ($application->monthly_interest_rate * $application->principal_amount) / 100;
    $total_interest_amount = $monthly_interest_amount * $application->loan_tenure;
    $monthly_installment = ($application->principal_amount / $application->loan_tenure) + $monthly_interest_amount;
    $total_repayment = $total_interest_amount + $application->principal_amount;
    $customer_id = $application->customer_id;
    $principal_amount = $application->principal_amount;
    $monthly_interest_rate = $application->monthly_interest_rate;


    $product = Products::where('id', '=', $application->loan_product_id)->first();

    $customer = Customers::select('customers.*', 'client_types.name as client_type_name')
        ->leftjoin('client_types', 'client_types.id', '=', 'customers.client_type_id')
        ->where('customers.id', '=', $customer_id)
        ->first();

    $documents = ProductDocuments::leftjoin('documents', 'documents.id', '=', 'loan_products_documents.document_id')
        ->select('loan_products_documents.*', 'documents.name as document_name')
        ->where('loan_products_documents.loan_product_id', '=', $application->loan_product_id)
        ->get();
    $loan_tenure = $application->loan_tenure;

    if ($isBot) {
        return [
            'customer' => $customer,
            'interest_rate' => $monthly_interest_rate,
            'monthly_interest_rate' => $monthly_interest_rate,
            'documents' => $documents,
            'monthly_interest_amount' => $monthly_interest_amount,
            'total_interest_amount' => $total_interest_amount,
            'monthly_installment' => $monthly_installment,
            'installment_amount' => $monthly_installment,
            'total_repayment' => $total_repayment,
            'product' => $product,
            'principal_amount' => $principal_amount,
            'loan_tenure' => $loan_tenure,
            'loan_product_id' => $application->loan_product_id,
            'customer_id' => $customer_id,
            'status' => 'PENDING',
            'loan_application_reference' => generateLoanApplicationReference()
        ];

    }
    return array($customer, $monthly_interest_rate, $documents, $monthly_interest_amount, $total_interest_amount, $monthly_installment, $total_repayment, $documents, $product, $principal_amount, $loan_tenure);
}


function generateLoanApplicationReference()
{
    try {
        $date = Carbon::now();
        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');
        $lastRecord = DB::table('loan_applications')->latest()->first();
        $counter = $lastRecord ? $lastRecord->id + 1 : 1;
        $loanApplicationReference = sprintf('%s-%s%s%s-%d', 'APP', $day, $month, $year, $counter);

        return $loanApplicationReference;
    } catch (Exception $e) {
        toast('Failed to generate reference', 'error');
        return back();
    }
}
