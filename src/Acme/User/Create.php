<?php
namespace
	Acme\User;
	
use 
	Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface;

class Create extends Command {
	protected function configure() {
		$this
            ->setName('user:create')
            ->setDescription('Creates a new user')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Your name'
            )->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Your email'
            );
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$user = array(
			'name' => $input->getArgument('name'),
			'email' => $input->getArgument('email')
		);

        $ch = curl_init("http://127.0.0.1/user");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $user);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);

		$output->writeln($response);
	}

}