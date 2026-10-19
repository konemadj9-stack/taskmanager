terraform {
  required_version = ">= 1.5.0"

  required_providers {
    virtualbox = {
      source  = "terra-farm/virtualbox"
      version = "0.2.2-alpha.1"
    }
  }
}

provider "virtualbox" {}

resource "virtualbox_vm" "taskmanager" {
  name   = var.vm_name
  image  = "https://vagrantcloud.com/bento/boxes/ubuntu-22.04/versions/202510.26.0/providers/virtualbox/amd64/vagrant.box"
  cpus   = var.vm_cpus
  memory = "${var.vm_memory_mib} mib"

  network_adapter {
    type = "nat"
  }

  network_adapter {
    type           = "hostonly"
    host_interface = var.hostonly_interface
  }
}
