<?php
namespace
	Acme\User;
	
use 
	Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface;

class Update extends Command {
	protected function configure() {
		$this
            ->setName('user:update')
            ->setDescription('Creates a new user')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'User name'
            )->addArgument(
                'email',
                InputArgument::REQUIRED,
                'User email'
            )->addArgument(
                'id',
                InputArgument::REQUIRED,
                'User ID'
            );
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$user = array(
			'name' => $input->getArgument('name'),
			'email' => $input->getArgument('email')
		);

		$id = $input->getArgument('id');

        $ch = curl_init("http://127.0.0.1/user/" . $id); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($user));
 
        $response = curl_exec($ch);

        $output->writeln($response);
	}

}