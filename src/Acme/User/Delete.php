<?php
namespace
	Acme\User;
	
use 
	Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface;

class Delete extends Command {
	protected function configure() {
		$this
            ->setName('user:delete')
            ->setDescription('Creates a new user')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'User ID'
            );
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$id = $input->getArgument('id');

        $ch = curl_init("http://127.0.0.1/user/" . $id); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
 
        $response = curl_exec($ch);

       	$output->writeln($response);
	}

}