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

echo "✅ Setup complete! Test SSH access using:"
echo "ssh lubuntu@lubuntu.local"

# Placeholder for future steps
echo "🚧 Additional setup steps will be added here later..."
