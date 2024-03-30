<?php
 
namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
 
class DBConn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DBConnection';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test DB Connection';
 
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Attempt to establish a connection to the database
            $pdo = DB::connection()->getPdo();
            // Connection succeeded
            echo "Database connection established successfully!";
        } catch (\Exception $e) {
            // Connection failed
            echo "Error establishing database connection: " . $e->getMessage();
        }
    }
}