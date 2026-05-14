use Illuminate\Support\Facades\Route;

Route::get('/students', function () {
    return response()->json([
        ['id' => 1, 'name' => 'Andi Pratama', 'nim' => '21610001'],
        ['id' => 2, 'name' => 'Siti Rahma', 'nim' => '21610002'],
    ]);
});