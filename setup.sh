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

# Step 3: Configure Static IP
echo "🔧 Configuring static IP..."
read -p "Enter your desired static IP (e.g., 192.168.61.100): " static_ip
read -p "Enter your gateway IP (e.g., 192.168.61.1): " gateway_ip
read -p "Enter your DNS servers (e.g., 8.8.8.8,8.8.4.4): " dns_servers

nmcli connection modify "Wired connection 1" ipv4.method manual ipv4.addresses "$static_ip/24" ipv4.gateway "$gateway_ip" ipv4.dns "$dns_servers"
nmcli connection down "Wired connection 1"
nmcli connection up "Wired connection 1"
echo "✅ Static IP configured as $static_ip."

echo "✅ Setup complete! Test SSH access using:"
echo "ssh lubuntu@lubuntu.local"

# Placeholder for future steps
echo "🚧 Additional setup steps will be added here later..."
