<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DemoVentasSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            if (DB::table('customers')->where('dni', '99000001')->exists()) {
                throw new \RuntimeException('Los datos demo ya existen. No ejecutar nuevamente.');
            }
            $categories = DB::table('categories')->orderBy('id')->pluck('id');
            $user = DB::table('users')->orderBy('id')->first();
            if ($categories->isEmpty() || !$user) throw new \RuntimeException('Faltan categorías o usuarios.');
            $now = now();
            $clients = []; $meds = [];
            for ($i=0; $i<10; $i++) {
                $clients[] = DB::table('customers')->insertGetId([
                    'name'=>'Cliente DEMO '.($i+1), 'dni'=>sprintf('99%06d',$i+1),
                    'created_at'=>$now,'updated_at'=>$now
                ]);
                $price = 3.50 + $i*2.25;
                $id = DB::table('medicines')->insertGetId([
                    'name'=>'Medicamento DEMO '.($i+1), 'category_id'=>$categories[$i % $categories->count()],
                    'price'=>$price,'stock'=>($i<2?10:100),'created_at'=>$now,'updated_at'=>$now
                ]);
                $meds[] = ['id'=>$id,'price'=>$price];
            }
            for ($i=0; $i<10; $i++) {
                $date = $now->copy()->subDays(6-($i%7));
                $m = $meds[$i]; $amount = 1+($i%5); $total = round($m['price']*$amount,2);
                $sale = DB::table('sales')->insertGetId([
                    'customer_id'=>$clients[$i],'user_id'=>$user->id,'date'=>$date,
                    'total'=>$total,'igv'=>round($total*18/118,2),'created_at'=>$date,'updated_at'=>$date
                ]);
                DB::table('detail_sales')->insert([
                    'sale_id'=>$sale,'medicine_id'=>$m['id'],'amount'=>$amount,
                    'price_unit'=>$m['price'],'subtotal'=>$total,'created_at'=>$date,'updated_at'=>$date
                ]);
                DB::table('medicines')->where('id',$m['id'])->decrement('stock',$amount);
            }
        });
    }
}
