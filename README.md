# php-ipfinder
A simple tool to enumerate various info about a single or multiple IP addresses

## Overview
The tool relies on [IPINFO](https://ipinfo.io), implementing a client to interact and extract interesting information regarding geolocation and general information.
Additionally, the tool uses `phpwhois/phpwhois` as the WhoIs library, while the TorChecker and BLChecker use a custom implementation to check the presence of the IP into publicly available lists.

## Features

Currently, the features implemented in [php-ipfinder][1] are:

* Geolocation
* WhoIs Information 
* Tor Exit-Node Identification
* Public Blacklisted IP Identification

## Usage

The tool takes a single IP or a list (file), and return the analysis output as an Excel file or a console table.
Additionally, the tool permits to specify an API key to be used against [IPINFO](https://ipinfo.io). 

```
Usage:
    php php-ipfinder [-i <IP>] [-f <IP List file>] [-o <Output File>] [-k <API-KEY>]
    -i: Single IP Address to search
    -f: File with a list of IP Address to search
    -o: Output file name without extension (the format is .xlsx)
    -k: API Key for https://ipinfo.io (free account gives 50k monthly requests)
```

## Utils

IPFinder is provided with two additional utils:

* merger: Merge multiple results files into a single file (xlsx)

```
Usage:
    php merger.php -t <target_directory>
```
* splitter: Old utility that tries to take a file and extract an IP list from them. 
    - Supports: xls, txt and csv files

```
Usage:
    php splitter.php <target_file> <limit>
```

## Known Issues

- Table output: Column values get truncated if length > 24 chars
- Low performance: The script takes ~20s for single IP

## Considerations

This is quite an old tool used for a brief initial recon of an IP Addresses from a defensive perspective.
While it still does its work, it's quite slow and needs some major performance improvement in order to be effective.

[1]:https://github.com/klezVirus/php-ipfinder