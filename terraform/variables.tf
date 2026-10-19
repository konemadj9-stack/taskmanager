variable "vm_name" {
  description = "Nom de la VM VirtualBox créée par Terraform."
  type        = string
  default     = "taskmanager-ubuntu"
}

variable "vm_cpus" {
  description = "Nombre de processeurs virtuels."
  type        = number
  default     = 2

  validation {
    condition     = var.vm_cpus >= 1 && var.vm_cpus <= 4
    error_message = "vm_cpus doit être compris entre 1 et 4 pour ce laboratoire."
  }
}

variable "vm_memory_mib" {
  description = "Mémoire de la VM en MiB."
  type        = number
  default     = 2048

  validation {
    condition     = var.vm_memory_mib >= 1024 && var.vm_memory_mib <= 8192
    error_message = "vm_memory_mib doit être compris entre 1024 et 8192."
  }
}

variable "hostonly_interface" {
  description = "Nom exact de l'interface réseau Host-only de VirtualBox sur l'hôte."
  type        = string

  validation {
    condition     = length(trimspace(var.hostonly_interface)) > 0
    error_message = "Renseignez le nom de l'interface Host-only dans terraform.tfvars."
  }
}
