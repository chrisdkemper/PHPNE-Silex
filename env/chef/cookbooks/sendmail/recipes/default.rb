#
# Cookbook Name:: sendmail
# Recipe:: default
#
# Copyright 2012, YOUR_COMPANY_NAME
#
# All rights reserved - Do Not Redistribute
#
execute "Update hosts file to include sendmail domain" do
	command "sudo sh -c \"echo '127.0.1.1       precise64 precise64.' >> /etc/hosts\""
	action :run
end

package "sendmail" do
	action :install
end