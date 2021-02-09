<?php

namespace App\Export;

use App\Models\Order;
use App\Models\OrderedItem;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class export implements FromCollection
{
    use Exportable;
    private $headings = [
                                     'Order Id' ,
                                    'Session Name',
                                    'Customer Name',
                                    'Mobile',
                                    'Email',
                                    'Date & Time',
    ];

    protected $filter;

    function __construct($filter) {

        $this->filter = $filter;
    }

    public function map($id): array
    {
       $policy = Policy::where('id',$id)->first();
       $created_at = $policy->created_at;
       $premium_freq = $policy->premium_freq;
       $transaction_sub = 'N/A';
       $zip = 'N/A';
       $cov_a = 'N/A';

       if($policy->policyNumber)
           $policyNumber = $policy->policyNumber;
       else
           $policyNumber = 'N/A';

       if($policy->customer_id != null) {
           $customer = Customer::where('id', $policy->customer_id)->first();
           if($customer != null) {
               $name = $customer->firstName;
           }
           else {
               $name = 'N/A';
           }
       }else{
           $name = 'N/A';
       }

       if($policy->policyNumber != null) {
           $policy = Policy::where('policyNumber',$policy->policyNumber)->first();
           if($policy != null) {
               $ledger = Ledger::where('customer_id',$policy->customer_id)->first();
               if($ledger){
                   $transaction = $ledger->orig_trans;
               }
               else {
                   $transaction = 'N/A';
               }
           }
           else{
               $transaction = 'N/A';
           }
       }
       else{
           $transaction = 'N/A';
       }

       if($policy->policyNumber !=null){
           $policy = Policy::where('policyNumber',$policy->policyNumber)->first();
           if($policy){
               $customerProfile = CustomerProfile::where('customer_id',$policy->customer_id)->first();
               if($customerProfile){
                   $address = $customerProfile->address;
               }
               else{
                   $address = 'N/A';
               }
           }
           else{
               $address = 'N/A';
           }
       }
       else{
           $address = 'N/A';
       }

       if($policy->policyNumber !=null){
           $CustomerProfile = CustomerProfile::where('customer_id',$policy->customer_id)->first();
           if($CustomerProfile){
               $country = Country::where('id',$CustomerProfile->countryId)->first();
               if($country){
                   $country = $country->name;
               }
               else{
                   $country = 'Botswana';
               }
           }
           else{
               $country = 'Botswana';
           }
       }
       else{
           $country = 'Botswana';
       }

       if($policy->policyNumber !=null){
           $policy = Policy::where('policyNumber',$policy->policyNumber)->first();
           if($policy){
               $customerProfile = CustomerProfile::where('customer_id',$policy->customer_id)->first();
               if($customerProfile){
                   $city = $customerProfile->city;
               }
               else{
                   $city = 'N/A';
               }

           }
           else{
               $city = 'N/A';
           }
       }
       else{
           $city = 'N/A';
       }


       if($policy->policyNumber !=null){
           $CustomerProfile = CustomerProfile::where('customer_id',$policy->customer_id)->first();
           if($CustomerProfile){
               $state = State::where('id',$CustomerProfile->state)->first();
               if($state){
                   $state = $state->name;
               }
               else{
                   $state = 'NA';
               }
           }
           else{
               $state = 'NA';
           }
       }
       else{
           $state = 'NA';
       }




       return [
           $policyNumber,
           $name,
           $transaction,
           $premium_freq,
           $transaction_sub,
           $name,
           $address,
           $country,
           $city,
           $state,
           $zip,
           $cov_a,
           $created_at,
       ];
    }


    public function collection()
    {

        $query = Order::get();
       $query = Policy::orderBy('created_at', 'DESC');
       if ($this->filter['filterDateFrom'] != '-1' && $this->filter['filterDateto'] != '-1')
       {
           $query->whereBetween(DB::raw('date(created_at)') , [Carbon::parse($this->filter['filterDateFrom'])
               ->format('Y-m-d') , Carbon::parse($this->filter['filterDateto'])
               ->format('Y-m-d') ]);
       }elseif ($this->filter['filterDateFrom'] != '-1' && $this->filter['filterDateto'] == '-1'){
           //Carbon::parse('today')
           $query->whereBetween(DB::raw('date(created_at)') , [Carbon::parse($this->filter['filterDateFrom'])
               ->format('Y-m-d') , Carbon::parse('today')
               ->format('Y-m-d') ]);
       }
       $query = $query->get();
       return $query->pluck('id');
        return $query;
    }
    public function headings() : array
    {
        return $this->headings;
    }
}