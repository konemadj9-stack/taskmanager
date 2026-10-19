param(
    [Parameter(Position = 0)]
    [string] $Action = 'help',

    [Parameter(Position = 1)]
    [ValidateRange(1, 65535)]
    [int] $Port = 8080
)

$ErrorActionPreference = 'Stop'

$ContainerName = 'taskmanager-jenkins'
$VolumeName = 'taskmanager_jenkins_home'
$ImageName = 'jenkins/jenkins:lts-jdk21'

function Test-DockerAvailable {
    & docker info --format '{{.ServerVersion}}' 2>$null | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw 'Docker ne repond pas. Demarre Docker Desktop puis relance cette commande.'
    }
}

function Get-ContainerStatus {
    $status = & docker container inspect --format '{{.State.Status}}' $ContainerName 2>$null
    if ($LASTEXITCODE -ne 0) {
        return $null
    }

    return [string] $status
}

function Start-Jenkins {
    $status = Get-ContainerStatus

    if ($null -ne $status) {
        if ($status -eq 'running') {
            Write-Host "Jenkins est deja demarre : http://localhost:$Port"
            return
        }

        & docker start $ContainerName
        if ($LASTEXITCODE -ne 0) {
            throw "Impossible de redemarrer le conteneur $ContainerName."
        }

        Write-Host "Conteneur Jenkins redemarre. URL : http://localhost:$Port"
        Write-Host 'Pour afficher le mot de passe initial : .\make.ps1 password'
        return
    }

    & docker volume create $VolumeName | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw "Impossible de creer le volume Docker $VolumeName."
    }

    & docker run --detach `
        --name $ContainerName `
        --restart unless-stopped `
        --publish "127.0.0.1:${Port}:8080" `
        --volume "${VolumeName}:/var/jenkins_home" `
        $ImageName

    if ($LASTEXITCODE -ne 0) {
        throw 'La creation du conteneur Jenkins a echoue.'
    }

    Write-Host ''
    Write-Host "Jenkins demarre en arriere-plan : http://localhost:$Port"
    Write-Host 'Le premier demarrage peut prendre quelques minutes.'
    Write-Host 'Pour afficher le mot de passe initial : .\make.ps1 password'
    Write-Host 'Pour suivre les journaux : .\make.ps1 logs'
}

try {
    $normalizedAction = $Action.ToLowerInvariant()
    if ($normalizedAction -eq 'jenkis') {
        $normalizedAction = 'jenkins'
    }

    switch ($normalizedAction) {
        'jenkins' {
            Test-DockerAvailable
            Start-Jenkins
        }
        'status' {
            Test-DockerAvailable
            & docker ps --filter "name=^/$ContainerName$" --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'
            if ($LASTEXITCODE -ne 0) {
                throw 'Impossible de lire le statut des conteneurs Docker.'
            }
        }
        'logs' {
            Test-DockerAvailable
            if ((Get-ContainerStatus) -ne 'running') {
                throw "Jenkins n'est pas demarre. Lance d'abord .\make.ps1 jenkins."
            }

            & docker logs --follow --tail 100 $ContainerName
            if ($LASTEXITCODE -ne 0) {
                throw 'Impossible de lire les journaux Jenkins.'
            }
        }
        'password' {
            Test-DockerAvailable
            if ((Get-ContainerStatus) -ne 'running') {
                throw "Jenkins n'est pas demarre. Lance d'abord .\make.ps1 jenkins."
            }

            for ($attempt = 0; $attempt -lt 30; $attempt++) {
                $password = & docker exec $ContainerName cat /var/jenkins_home/secrets/initialAdminPassword 2>$null
                if ($LASTEXITCODE -eq 0 -and -not [string]::IsNullOrWhiteSpace(($password -join ''))) {
                    Write-Host ($password -join '')
                    exit 0
                }

                Start-Sleep -Seconds 2
            }

            throw 'Le mot de passe initial est indisponible. Jenkins a peut-etre termine son assistant de configuration ; consulte .\make.ps1 logs.'
        }
        'stop' {
            Test-DockerAvailable
            if ((Get-ContainerStatus) -eq 'running') {
                & docker stop $ContainerName
                if ($LASTEXITCODE -ne 0) {
                    throw "Impossible d'arreter Jenkins."
                }
            }
            else {
                Write-Host "Jenkins est deja arrete ou n'existe pas encore."
            }
        }
        'help' {
            Write-Host 'Usage : .\make.ps1 <commande> [port]'
            Write-Host ''
            Write-Host '  jenkins  Demarre ou redemarre Jenkins (alias accepte : jenkis)'
            Write-Host '  status   Affiche le statut du conteneur'
            Write-Host '  logs     Suit les journaux Jenkins (Ctrl+C pour quitter)'
            Write-Host '  password Affiche le mot de passe initial Jenkins'
            Write-Host '  stop     Arrete Jenkins sans supprimer ses donnees'
            Write-Host ''
            Write-Host 'Exemple : .\make.ps1 jenkins 8081'
        }
        default {
            throw "Commande inconnue : $Action. Utilise .\make.ps1 help."
        }
    }
}
catch {
    Write-Error $_.Exception.Message
    exit 1
}
