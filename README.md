Fejlesztő környezet
-------------------

Az alábbiak előfeltétele, hogy az app mappában nyomjunk egy `composer install`-t. Ez valószínűleg nincs lokálisan telepítve, így ajánlott a [telepítési útmutatót](https://getcomposer.org/download/) követni.

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

1. indítsuk el a dev környezetet, ahogy felül írva vagyon.

2. írjunk egy ütős feature-t és hozzá tartozó forgatókönyveket a `features` könyvtárban. Tippek: [features and scenarios](http://docs.behat.org/en/latest/user_guide/features_scenarios.html)
	
3. kódoljunk app: https://project.local, logok: localhost:8080

4. teszteljünk, hogy sikerült-e a kódunk: `docker exec -ti project.local /var/www/project/test.sh /var/www/project`, a tesztet localhoston futó VNC szerveren keresztül hátradőlve élvezhetjük. A test.sh a behat wrapper-e, a második argumentuma után fogadja a behat argumentumokat. pl. `docker exec -ti project.local /var/www/project/test.sh /var/www/project --help`

5. sikeresesen lefutó teszt után `git commit` és `git push`


Features
--------

Újból felhasználható step-eket csináltunk, amivel resetelni lehet a hexaa adatokat (delete all), valamint alap teszt adatokkal lehet feltölteni. [Bővebben](https://git.hbit.sztaki.hu/solazs/hexaa-test-data-manager/tree/master)

A stepek:
```
Given delete all hexaa data
Given setup the basic hexaa test data
```

A stepek itt vannak kifejtve: (app/src/AppBundle/Features/hexaa-test-data-manager.feature)

Demo környezet
--------------

https://server.hexaa.eu/ui/


UX specifikáció
---------------

[ui-design](doc/ui-design)