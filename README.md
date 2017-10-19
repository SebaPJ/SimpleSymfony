Simple Symfony Project
========================

## Description

Projekt ma na celu przedstawienie podstawowego użycia frameworka Symfony, w oparciu o model zarządzania kontaktami telefonicznymi w bazie danych.  

## Installation

Instalacja systemu odbywa się automatycznie. Automatycznie instalują się także potrzebne componenty.
Aby instalacja sie rozpoczęła należy wpisać poniższe polecenia w okno konsoli projektu.

Po zakończeniu isntalacji, maszyna wirtualna bedzie dostępna pod adresem: 192.168.100.104

```sh
$ ./install/prepare_enviroment.sh
$ vagrant up
```

Uwaga: Instalacja moze potrwać parenaście minut za pierwszym razem, ze wzgledu na tworzenie się maszyny wirtualnej i konfigurację wymaganych komponentów.

## Tests

Aby uruchomić testy należy wykonać w oknie konsoli projektu poniższą linijkę kodu.

```sh
$ phpunit
```