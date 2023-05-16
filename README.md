# Winback Interface

Winback Interface est une plateforme d'administration pour gérer les machines Winback connectées sur le serveur.
## How to use:

### Start Server:
Open a terminal and run command: ```symfony console app:tcpserver```

This will create a new server and connect with devices.
If the server starts correctly, it prints server connexion information, device information and commands received.

### Start Interface:
Open a terminal and run command: ```symfony console start:server```

Libraries to install:
composer require twig/intl-extra

## Installation

Use the package manager [pip](https://pip.pypa.io/en/stable/) to install foobar.

```bash
pip install foobar
```

## Usage

```python
import foobar

# returns 'words'
foobar.pluralize('word')

# returns 'geese'
foobar.pluralize('goose')

# returns 'phenomenon'
foobar.singularize('phenomena')
```



## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Documentation

Create new Command:

- get contentSize, to define data space in header
- create header
- if numbers, pass them to setResponseToByte
- if string, concatenate directly to response
- concatenate header & response
- after command switch, response is concatenated with footer