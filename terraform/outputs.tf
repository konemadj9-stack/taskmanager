output "taskmanager_ip" {
  description = "Adresse IPv4 de la VM sur le réseau Host-only."
  value       = virtualbox_vm.taskmanager.network_adapter[1].ipv4_address
}
