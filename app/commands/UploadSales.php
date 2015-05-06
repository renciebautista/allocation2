<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UploadSales extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'upload:sales';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Upload new sales.';

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
	public function fire()
	{
		$this->line('Uploading new sales.');
		Artisan::call('db:seed', array('--class' => 'UserTableTableSeeder'));
	}

}
