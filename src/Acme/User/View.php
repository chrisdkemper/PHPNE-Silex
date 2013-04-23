<?php
namespace
	Acme\User;
	
use 
	Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface;

class View extends Command {
	protected function configure() {
		$this
            ->setName('user:view')
            ->setDescription('Creates a new user')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'User ID'
            );
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$id = $input->getArgument('id');

		$response = file_get_contents('http://127.0.0.1/user/' . $id);

		$output->writeln($response);
	}

}