INSERT INTO
  pages (name, title_et, title_en, title_ru, content_et)
VALUES
  ("index", "Indeks", "Index", "Индекс", "Esileht..."),
  ("prices", "Hinnakiri", "Prices", "Ценник", "..."),
  ("rooms", "Toad", "Rooms", "Комнаты", "..."),
  ("pub", "Pubi", "Pub", "Духан", "..."),
  ("sauna", "Saun", "Sauna", "Баня", "..."),
  ("events", "Üritused", "Events", "Делы", "..."),
  ("special", "Eripakkumised", "Special offers", "Поднесение", "..."),
  ("carrent", "Autorent", "Car rent", "Рента автомобиль", "..."),
  ("location", "Asukoht", "Location", "Положение", "...");


INSERT INTO users VALUES (1, 'nene', sha1('admin'));


UPDATE pages SET
content_et = 'Kambja hotell asub 15 km Tartust Võru poole, maalilise Tatra oru nõlval.

Hotellis on 45 majutuskohta, 2 sauna ja 3 erineva suurusega saali, mis sobivad ürituste läbiviimiseks.

Hotellis asub ka Kambja Pubi, milles pakume igasugu asju.

Broneerimine
------------

E-post: info@kindlusgrupp.ee<br />
Tel: +372 711 4497<br />
Tel: +372 509 3253
'
WHERE name='index';



UPDATE pages SET
content_et = 'Hinnakiri
---------

###Toad

<table>
<tr><td>Kahekohaline tuba</td><td>650.– EEK</td></tr>
<tr><td>Kahekohaline tuba ühele</td><td>390.– EEK</td></tr>
<tr><td>Peretuba (2 täisk. + 2 last)</td><td>990.– EEK</td></tr>
<tr><td>Lisavoodi</td><td>160.– EEK</td></tr>
</table>

###Hommikusöök

Hommikubuffet restoranis

<table>
<tr><td>E–R</td><td>7.00–10.00</td></tr>
<tr><td>L, P</td><td>7.00–11.00</td></tr>
</table>

###Konverentsisaal

500.– EEK/tund,<br />
2800.– EEK/päev.

Saali mahub 40-120 inimest.

### Saun

Nii soome saun kui aurusaun 200.– EEK/tund.

### Vesipiibutuba
'
WHERE name='prices';



UPDATE pages SET
content_et = 'Asukoht
-------

<!-- Google Maps Element Code -->
<div id="map"><iframe frameborder="0" marginwidth="0" marginheight="0" border="0" style="border:0;margin:0;width:100%;height:375px;" src="http://www.google.com/uds/modules/elements/mapselement/iframe.html?maptype=roadmap&amp;latlng=58.242603062493984%2C26.70055389404297&amp;mlatlng=58.235291%2C26.694421&amp;maddress1=Kambja&amp;maddress2=Estonia&amp;zoom=13&amp;mtitle=Kambja%20Hotell" scrolling="no" allowtransparency="true"></iframe></div>
'
WHERE name='location';

UPDATE pages SET content_en = content_et WHERE name='location';
UPDATE pages SET content_ru = content_et WHERE name='location';
