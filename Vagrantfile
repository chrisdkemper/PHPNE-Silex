Vagrant::Config.run do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  config.vm.forward_port 80, 80
  config.vm.forward_port 3306, 3306
  config.vm.forward_port 443, 443
  config.vm.network :hostonly, "33.33.33.33"
  config.vm.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  config.vm.customize ["modifyvm", :id, "--memory", "1024"]
  config.ssh.max_tries = 50
  config.ssh.timeout = 300
  Vagrant::Config.run do |config|
    config.vm.share_folder "v-root", "/vagrant", ".", :nfs => true
    config.vm.provision :shell, :path => "env/pre-provision.sh"
    config.vm.provision :chef_solo do |chef|
    chef.log_level = :info
    chef.cookbooks_path = "env/chef/cookbooks"
    chef.add_recipe "application"
    chef.json = {
      :mysql => {
	:server_root_password => 'root',
	:allow_remote_root => true,
	:bind_address => "0.0.0.0"
      }
    }
    end
    config.vm.provision :shell, :path => "env/post-provision.sh"
  end
end
