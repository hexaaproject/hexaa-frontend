Hexaa-frontend
========================

HEXAA (Higher Education External Attribute Authority) is a new External Attribute Provider designed to be used in SAML federations.

Functionality includes:

+ Virtual Organization management
+ Storage of personal (IdP-independent) attributes
+ Solutions for multiple applications: OpenNebula, Liferay and more!

You can find the instructions at the official documentation site at [hexaa.eu](http://hexaa.eu).

Installing HEXAA
----------------

Take a look at the ansible role:

[Ansible role for hexaa frontend](https://github.com/hexaaproject/ansible-role-hexaa-frontend)

[Ansible playbook with localhost config](https://github.com/hexaaproject/ansible-hexaa)


Docker image
-------------
https://hub.docker.com/r/hexaaproject/hexaa-frontend

Building from this repository:
```bash
docker build -t hexaaproject/hexaa-frontend:$TAG -f docker-prod/Dockerfile .
```
