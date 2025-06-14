#!/bin/bash

echo "🚀 Starting setup for Lubuntu VM..."

# Step 1: SSH Setup
echo "🔧 Setting up SSH..."
sudo apt update && sudo apt install -y openssh-server
sudo systemctl enable ssh
sudo systemctl start ssh
echo "✅ SSH service status:"
sudo systemctl status ssh | grep "Active:"

# Step 2: Set Hostname
echo "🔧 Setting hostname to 'lubuntu.local'..."
sudo hostnamectl set-hostname lubuntu.local
echo "127.0.0.1 lubuntu.local" | sudo tee -a /etc/hosts

# Step 3: Switch to DHCP
echo "🔧 Configuring network to use DHCP..."
nmcli connection modify "Wired connection 1" ipv4.method auto
nmcli connection down "Wired connection 1"
nmcli connection up "Wired connection 1"
echo "✅ Network configured to use DHCP."

# Step 4: FTP Setup
echo "🔧 Installing and configuring FTP server..."
sudo apt install -y vsftpd
sudo systemctl restart vsftpd

echo "🔧 Configuring vsftpd..."
sudo sed -i 's/^#write_enable=YES/write_enable=YES/' /etc/vsftpd.conf
sudo sed -i 's/^#local_enable=YES/local_enable=YES/' /etc/vsftpd.conf
sudo sed -i 's/^#chroot_local_user=YES/chroot_local_user=YES/' /etc/vsftpd.conf
sudo systemctl restart vsftpd
echo "✅ FTP server configured."

# Step 5: Create User 'cesi'
echo "🔧 Creating user 'cesi'..."
sudo adduser cesi --gecos "cesi,,,"
sudo usermod -d /home/lubuntu cesi
sudo usermod -aG lubuntu cesi
echo "✅ User 'cesi' created and added to group 'lubuntu'."

# Step 6: Configure Permissions
echo "🔧 Configuring permissions for '/home/lubuntu'..."
sudo chmod 770 /home/lubuntu
echo "✅ Permissions configured."

echo "✅ Setup complete! Test SSH access using:"
echo "ssh lubuntu@lubuntu.local"
echo "✅ Test FTP access using FileZilla with user 'cesi'."

# Placeholder for future steps
echo "🚧 Additional setup steps will be added here later..."
