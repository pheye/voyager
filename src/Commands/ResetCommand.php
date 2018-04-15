<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;

class ResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voyager:resetpwd {email : Email }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置密码为password123456';

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
        $email = $this->argument('email');

        $model = config('voyager.user.namespace', 'App\\User');
        $user = $model::where('email', $email)->first();
        if ($user instanceof $model) {
            $user->password = bcrypt('password123456');
            $user->save();
            $this->info("$email reset password to 'password123456'");
            /* $this->info($user->usage); */
        } else {
            $this->error("no such user:" . $email);
            return;
        }
        
    }
}
