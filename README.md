[![pipeline status](https://git.hbit.sztaki.hu/hexaa/hexaa-newui/badges/master/pipeline.svg)](https://git.hbit.sztaki.hu/hexaa/hexaa-newui/commits/master)


Fejlesztő környezet
-------------------

Az alábbiak előfeltétele, hogy az app mappában nyomjunk egy `composer install`-t. Ez valószínűleg nincs lokálisan
telepítve, így ajánlott a [telepítési útmutatót](https://getcomposer.org/download/) követni.

1. Telepítsünk egy kellemes docker környezetet a gépünkre. Ne bízzunk a oprendszer disztribúcióban, a docke
 valószínűleg el van már avulva. Járjunk el a hivatalos telepítés mentén [docker.io](http://docker.io)

2. telepítsük a `docker-composer` rendszert is

3. jegyezzük be a `/etc/hosts` fileba a `127.0.0.1 project.local` domain nevet.

4. indítsuk el a docker fogatot:
`docker-compose -f docker/docker-compose-dev.yml up`
shibless fogat:
`docker-compose -f docker/docker-compose-fakeshib.yml up`

5. buildeljük le az appot (dependenciák, assetek.)
Ehhez be kell lépni a docker konténerbe, és ott buildelni (ott van php környezet)
`docker exec -ti project.local bash`
`cd /var/www/project`
`composer install`
`chown -R www-data /tmp/symfony/*`
`bin/console ass:dump`

6. böngészőben látogassunk el ide: `localhost:8080` itt találjuk a logokat (tailon)

7. böngészőben látogassunk el id: `https://project.local` és már indulunk is (a cert miatt sirmákolni fog a böngésző, de legyintsünk rá)

8. egy átlagos user azonosítója `e` jelszava `pass`

9. még üres az adatbázis? Így lehet megtölteni némi teszt adattal:


FAQ
-----

__Ha nem töltődnek be bizonyos build-elt js-ek és css-ek.__

Lépj be a szerverre és buildeltesd le az assetic-kel:

```
ssh ubuntu@newaai.niif.hu
cd hexaa-newui/app
php bin/console assetic:dump
```

Xdebug és phpstorm 
-------------------

(ez benne van a webapp runner containerben)
xdebug.remote_host=10.254.254.254
xdebug.remote_autostart=1
xdebug.idekey = PHPSTORM
xdebug.default_enable = 0
xdebug.remote_enable = 1
xdebug.remote_connect_back = 0
xdebug.profiler_enable = 1

sudo ifconfig en0 alias 10.254.254.254 255.255.255.0

+ DBGp proxy beállítás
 - host: 10.254.254.254
 - port: 9000
 - ide key: PHPSTORM

Git éa branch
-------------

Fejlesztési forgatókönyv

Ezt a metodikát próbáljuk követni:
`https://www.atlassian.com/git/tutorials/comparing-workflows#feature-branch-workflow`

1. keletkezik issue, mint feladat a Gitlab-ban
2. ha ott tart az issue, hogy érdemes kódolni, akkor a Gitlab-ban `New branch` gombbal már készíthetünk is egy új branch-et
3. lehetőség szerint a teszt megírásával kezdük, és a fejlesztés folyamán zöldítsük ki. keyword: BDD
4. ha új assetet gyártottunk, amit mások is fel tudnak használni, csináljunk egy snippetet, hogy hogyan kell használni: `https://git.hbit.sztaki.hu/hexaa/hexaa-newui/snippets`
5. futtassunk phpcs-t a kódra, mert a CI tesztelni fogja (kövi fejezet)
6. mehet a merge request a gitlabban a masterba. 

Coding standard
----------------
Telepítsük a Symfony2 CS-t egyszer:
`docker exec -t project.local /var/www/project/vendor/bin/phpcs --config-set installed_paths /var/www/project/vendor/escapestudios/symfony2-coding-standard`

Futtassuk le ezt commitok előtt, hogy lássuk, mennyit hibáztunk a symfony2 cs-hez képest
`docker exec -ti project.local /var/www/project/vendor/bin/phpcs --standard=Symfony2 /var/www/project/src/AppBundle`

Teszt
-----

1. indítsuk el a dev környezetet, ahogy felül írva vagyon.

2. írjunk egy ütős feature-t és hozzá tartozó forgatókönyveket a `features` könyvtárban.
    Tippek: [features and scenarios](http://docs.behat.org/en/latest/user_guide/features_scenarios.html)
	<http://docs.behat.org/en/v2.5/guides/1.gherkin.html>
3. kódoljunk app: https://project.local, logok: localhost:8080

4. teszteljünk, hogy sikerült-e a kódunk:
  `docker exec -ti project.local /var/www/project/vendor/bin/behat -c /var/www/project/behat.yml`,
  a tesztet localhoston futó VNC szerveren keresztül hátradőlve élvezhetjük. A test.sh a behat wrapper-e, a második
  argumentuma után fogadja a behat argumentumokat. pl.
  `docker exec -ti project.local /var/www/project/vendor/bin/behat -c /var/www/project/behat.yml --help`

5. navigáljunk ide: `http://localhost:6080`, és hátradőlve nézhetjük, ahogy a robot helyettünk kattintgatva tesztel

6. sikeresesen lefutó teszt után `git commit` és `git push`


Features
--------

Újból felhasználható step-eket csináltunk, amivel resetelni lehet a hexaa adatokat (delete all), valamint alap teszt adatokkal lehet feltölteni. [Bővebben](https://git.hbit.sztaki.hu/solazs/hexaa-test-data-manager/tree/master)

A stepek:
```
Given emtpy hexaa data
Given setup the basic hexaa test data
```

Bővebben kifejtve: [hexaa-test-data-manager.feature](app/src/AppBundle/Features/hexaa-test-data-manager.feature)


Demo környezet
--------------

https://server.hexaa.eu/ui/


UX specifikáció
---------------

[ui-design](doc/ui-design)

Validálások a View rétegben (javascript, jquery)
------------------------------------------------

A base.html.twig-ben már betöltjük a validáló rendszert, leírás itt van:

https://jqueryvalidation.org/

Github szelet
-------------

git remote add github git@github.com:hexaaproject/hexaa-newui.git
csak az app könyvtárat publikáljuk:

git subtree push --prefix app github master
