# Security

## Config example

```json

{
  "security": {
    "modes": [
      {
        "mode": "basic_auth",
	"config": {
		"username": "john",
		"password": "doe"
     	}
      },
      { 
	"mode": "ip_white_list",
	"config": {
		"ips": ["127.0.0.1"]
	}
      }
    ]
  }
}

```
