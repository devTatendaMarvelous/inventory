<?php

namespace App\Traits;

use App\Services\Claims\ReportServiceResolver;
use Illuminate\Support\Facades\Session;

trait HasGetters
{
    use ApiConsume;
    function getBranches()
    {
        $branches = [];
        $response = $this->getRequest('/branches/find-all');
        if ($response->status == 200) {
            $branches = $response->content->dataList;
        }
        Session::put('branches', $branches);
        return $branches;
    }
    function getClaims($type,$policy_id)
    {
        $claims = [];
        $response = $this->getRequest("/$type-claims/get-claims-by-policy-id/$policy_id",'claims');

        if ($response->status == 200) {
            $claims = $response->content->dataList;
        }
        Session::put('claims', $claims);
        return $claims;

    }
    function getProducts()
    {
        $products = [];
        $response = $this->getRequest('/products/find-all?pageNumber=0&pageSize=1000');

        if ($response->status == 200) {
            $products = $response->content->pagedList->content;
        }
        Session::put('products', $products);
        return $products;
    }
    function getLegalFirms()
    {
        $firms = [];
        $response = $this->getRequest('/legalFirms/find-all','claims');

        if ($response->status == 200) {
            $firms = $response->content->dataList;
        }
        Session::put('legalFirms', $firms);
        return $firms;
    }
    function getTypeOFCases()
    {
        $types = [];
        $response = $this->getRequest('/type-of-cases/find-all','claims');

        if ($response->status == 200) {
            $types = $response->content->dataList;
        }
        Session::put('typeOFCases', $types);
        return $types;
    }
    function getNatureOfCases()
    {
        $natures = [];
        $response = $this->getRequest('/nature-of-cases/find-all','claims');

        if ($response->status == 200) {
            $natures = $response->content->dataList;
        }
        Session::put('natureOfCases', $natures);
        return $natures;
    }
    function getCurrencies()
    {
        $currencies = [];
        $response = $this->getRequest('/rates/all');

        if ($response->status == 200) {
            $currencies = $response->content->dataList;
        }
        Session::put('currencies', $currencies);
        return $currencies;
    }
    function getProductTypes()
    {
        $productTypes = [];
        $response = $this->getRequest('/product-types/find-all');

        if ($response->status == 200) {
            $productTypes = $response->content->dataList;
        }
        Session::put('productTypes', $productTypes);
        return $productTypes;
    }
    function getAgents()
    {
        $agents = [];
        $response = $this->getRequest('/agents/find-all?pageNumber=0&pageSize=10000000');

        if ($response->status == 200) {
            $agents = $response->content->pagedList->content;
        }
        Session::put('agents', $agents);
        return $agents;
    }

    function getEmployers($remove=''): array
    {
        $payers = [];
        $response = $this->getRequest('/payers');

        if ($response->status == 200) {
            $payers = $response->content;
        }
        Session::put('payers', $payers);
        return $payers;
    }
    function getPaymentMethods($remove=''): array
    {
        $methods = [
            "CASH",
            "DIRECT_DEBIT",
            "ECOCASH",
            "SSB",
            "SSB_USD",
            "CASH_USD",
            "CASH_ZWL",
            "DIRECT_DEBIT_USD",
            "DIRECT_DEBIT_ZWL",
            "ECOCASH_ZWL",
            "ECOCASH_USD",
            "CASH_ZiG",
            "ECOCASH_ZiG",
            "SSB_ZiG",
            "DIRECT_DEBIT_ZiG"
        ];


        return array_values(array_diff($methods, [$remove]));
    }
    function getProvinces($remove=''): array
    {
        $provinces = [
            "HARARE METRO",
            "BULAWAYO METRO",
            "MASVINGO",
            "MIDLANDS",
            "MANICALAND",
            "MATEBELELAND NORTH",
            "MATEBELELAND SOUTH",
            "MASHONALAND CENTRAL",
            "MASHONALAND WEST",
            "MASHONALAND EAST"
        ];

        return array_values(array_diff($provinces, [$remove]));

    }
    function getMaritalStatus($remove=''): array
    {
        $statuses = [
            "SINGLE",
            "MARRIED",
            "DIVORCED",
            "WIDOWED"
        ];

        return array_values(array_diff($statuses, [$remove]));
    }
    function getTitles($remove=''): array
    {
        $titles= [
            "MR",
            "MRS",
            "MISS",
            "DR"
        ];

        return array_values(array_diff($titles, [$remove]));
    }

    function getRelations($remove='')
    {
        $relations = [
            "Son",
            "Daughter",
            "Husband",
            "Wife",
            "Father",
            "Sister",
            "Brother",
            "Mother",
            "Uncle",
            "Aunt",
            "Mother-In-Law",
            "Father-In-Law",
            "Grand-Father",
            "Grand-Mother",
            "Grand-Child",
            "Step-Child",
            "Niece",
            "Nephew",
            "Other",
        ];
        return array_values(array_diff($relations, [$remove]));

    }

    function getLapseReasons(): array
    {
        return [
            "AUTO UPGRADE",
            "CONTRACT ENDING",
            "CEASED BUT STILL DEDUCTING",
            "CONVERTED TO USD",
            "DEDUCTION WITHOUT CONSENT",
            "DOUBLE DEDUCTION",
            "INCAPACITATION",
            "MATURITY",
            "MISINFORMED",
            "NO LONGER INTERESTED",
            "NO REASON",
            "OVERDEDUCTION",
            "PERSONAL REASONS",
            "POOR SERVICE",
            "RELOCATING",
            "RETIRING",
            "SURRENDER",
            "SURRENDER BUT STILL DEDUCTING",
            "THE POLICY DOESN’T COVER DEBT COLLECTION",
            "TRIPPLE DEDUCTION"
        ];

    }
}
