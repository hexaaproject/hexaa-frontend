Fejlesztő környezet
-------------------

1. Telepítsünk egy kellemes docker környezetet a gépünkre. Ne bízzunk a oprendszer disztribúcióban, valószínűleg el van már avulva a docker. Járjunk el a hivatalos telepítés mentén http://docker.io

2. telepítsük a `docker-composer` rendszert is

3. jegyezzük be a `/etc/hosts` fileba a 127.0.0.1 project.local domnai nevet.

4. indítsuk el a docker fogatot:

`docker-compose -f docker/docker-compose-dev.yml up`

5. böngészőben látogassunk el ide: localhost:8080 itt találjuk a logokat (tailon)

6. böngészőben látogassunk el id: https://project.local és már indulunk is (a cert miatt sirmákolni fog a böngésző, de legyintsünk rá)

7. egy átlagos user azonosítója `e` jelszava `pass`

Shut up and hack.

