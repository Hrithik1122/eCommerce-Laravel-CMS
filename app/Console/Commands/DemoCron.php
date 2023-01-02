<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Product;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $product=Product::all();
         foreach ($product as $k) {
            if($k->special_price!=""){
                        $today = date('Y-m-d');
                        $start_date = date('Y-m-d', strtotime($k->special_price_start));
                        $end_date = date('Y-m-d', strtotime($k->special_price_to));
                        if($today>=$start_date&&$today<=$end_date){
                                $k->selling_price=(int)$k->special_price;
                                $dis_price=(int)($k->MRP)-(int)($k->special_price);
                                $disper=0;
                                if($dis_price!=0&&$dis_price>0){
                                    $disper=((int)$dis_price/(int)$k->MRP)*100;
                                }
                                $k->discount=(int)floor($disper);
                        }else{

                                $dis_price=(int)($k->MRP)-(int)($k->price);
                                $disper=0;
                                if($dis_price!=0&&$dis_price>0){
                                        $disper=((int)$dis_price/(int)$k->MRP)*100;
                                }
                                $k->discount=(int)floor($disper);
                                $k->selling_price=(int)$k->price;
                            }
                             $k->save();
                           }
            else{
                              $dis_price=(int)($k->MRP)-(int)($k->price);
                                $disper=0;
                                if($dis_price!=0&&$dis_price>0){
                                        $disper=((int)$dis_price/(int)$k->MRP)*100;
                                }
                                $k->discount=(int)floor($disper);
                                $k->selling_price=(int)$k->price;   
                                $k->save();
            }
                          
                          
         }
        
      \Log::info("Cron is working fine!");
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
