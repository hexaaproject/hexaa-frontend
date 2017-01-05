Fejlesztő környezet
-------------------

1. Telepítsünk egy kellemes docker környezetet a gépünkre. Ne bízzunk a oprendszer disztribúcióban, a docker valószínűleg el van már avulva. Járjunk el a hivatalos telepítés mentén [docker.io](http://docker.io)

2. telepítsük a `docker-composer` rendszert is

3. jegyezzük be a `/etc/hosts` fileba a 127.0.0.1 project.local domain nevet.

4. indítsuk el a docker fogatot:
`docker-compose -f docker/docker-compose-dev.yml up`

5. böngészőben látogassunk el ide: localhost:8080 itt találjuk a logokat (tailon)

6. böngészőben látogassunk el id: https://project.local és már indulunk is (a cert miatt sirmákolni fog a böngésző, de legyintsünk rá)

7. egy átlagos user azonosítója `e` jelszava `pass`


Teszt
-----

1. indítsuk el a selenium dockert, ami helyettünk kattintgatgat a böngészőben: `docker run --rm --name=grid -p 4444:24444 -p 5900:25900 --shm-size=1g --add-host="project.local:172.17.0.1" elgalu/selenium`


2. írjunk egy ütős feature-t és hozzá tartozó forgatókönyveket a `features` könyvtárban. Tippek: [features and scenarios](http://docs.behat.org/en/latest/user_guide/features_scenarios.html)
	
3. kódoljunk app: https://project.local, logok: localhost:8080

4. teszteljünk, hogy sikerült-e a kódunk: `vendor/bin/behat`, a tesztet localhoston futó VNC szerveren keresztül hátradőlve élvezhetjük

5. sikeresesen lefutó teszt után `git commit` és `git push`
