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
            $categoryIds = DB::table('categories')->pluck('id', 'name');
            $user = DB::table('users')->orderBy('id')->first();
            if ($categoryIds->isEmpty() || !$user) throw new \RuntimeException('Faltan categorías o usuarios.');
            $now = now();
            $clientNames = [
                'Mateo Huamán Salazar', 'Lucía Fernández Córdova', 'Diego Castillo Ramos',
                'Camila Navarro Soto', 'Sebastián Vargas León', 'Mariana Quispe Flores',
                'Andrés Mendoza Rojas', 'Sofía Herrera Pezo', 'Joaquín Paredes Gutiérrez',
                'Daniela Torres Chávez',
            ];
            $clientDetails = [
                ['912345601', 34], ['923456712', 28], ['934567823', 42], ['945678934', 21],
                ['956789045', 36], ['967890156', 64], ['978901267', 47], ['989012378', 19],
                ['901123489', 58], ['902234590', 31],
            ];
            $medicineData = [
                ['Ibuprofeno 400 mg x 10 tabletas', 'Antiinflamatorios', 3.50],
                ['Loratadina 10 mg x 10 tabletas', 'Antialérgicos', 3.20],
                ['Omeprazol 20 mg x 10 cápsulas', 'Gastrointestinales', 4.50],
                ['Vitamina C 1 g x 10 tabletas efervescentes', 'Vitaminas y suplementos', 12.00],
                ['Suero oral 500 ml', 'Gastrointestinales', 5.50],
                ['Hidrocortisona crema 1% 15 g', 'Dermatológicos', 8.90],
                ['Aspirina 100 mg x 30 tabletas', 'Analgésicos', 8.90],
                ['Ciprofloxacino 500 mg x 10 tabletas', 'Antibióticos', 7.20],
                ['Cefalexina 500 mg x 10 cápsulas', 'Antibióticos', 8.40],
                ['Paracetamol 500 mg x 10 tabletas', 'Analgésicos', 2.50],
            ];
            $clients = []; $meds = [];
            for ($i=0; $i<10; $i++) {
                [$phone, $age] = $clientDetails[$i];
                $clients[] = DB::table('customers')->insertGetId([
                    'name'=>$clientNames[$i], 'dni'=>sprintf('99%06d',$i+1),
                    'phone'=>$phone, 'age'=>$age,
                    'created_at'=>$now,'updated_at'=>$now
                ]);
                [$medicineName, $categoryName, $price] = $medicineData[$i];
                $id = DB::table('medicines')->insertGetId([
                    'name'=>$medicineName, 'category_id'=>$categoryIds[$categoryName],
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
