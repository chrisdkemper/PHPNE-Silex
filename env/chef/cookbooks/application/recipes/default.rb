execute "update apt-get database" do
  command "apt-get update --fix-missing"
end

execute "update/sync timezone" do
  command "echo \"Europe/London\" | sudo tee /etc/timezone && dpkg-reconfigure --frontend noninteractive tzdata"
end

include_recipe "openssl"
include_recipe "mysql::server"
include_recipe "apache2"
include_recipe "apache2::mod_php5"
include_recipe "apache2::mod_rewrite"
include_recipe "apache2::mod_ssl"
include_recipe "php"
include_recipe "php::module_mysql"
include_recipe "php::module_curl"
include_recipe "php::module_gd"
include_recipe "sendmail"

execute "disable-default-site" do
  command "sudo a2dissite default"
  notifies :reload, resources(:service => "apache2"), :delayed
end

execute "enable-php-additional-ini" do
  command "sudo ln -s /vagrant/env/php/additional.ini #{node['php']['ext_conf_dir']}/additional.ini"
  notifies :reload, resources(:service => "apache2"), :delayed
end

execute "create-ssl-cert" do
  command "sudo openssl req -new -x509 -days 365 -nodes -subj '/C=DC/ST=ST/L=L/CN=127.0.0.1' -out /etc/apache2/ssl/apache.pem -keyout /etc/apache2/ssl/apache.key"
  notifies :reload, resources(:service => "apache2"), :delayed
end

web_app "application" do
  template "application.conf.erb"
  notifies :reload, resources(:service => "apache2"), :delayed
end

php_pear "xdebug" do
  action :install
end

template "#{node['php']['ext_conf_dir']}/xdebug.ini" do
  source "xdebug.ini.erb"
  owner "root"
  group "root"
  mode "0644"
  action :create
  notifies :restart, resources("service[apache2]"), :delayed
end