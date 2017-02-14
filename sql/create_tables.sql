CREATE TABLE Kayttaja(
  id SERIAL PRIMARY KEY, -- SERIAL tyyppinen pääavain pitää huolen, että tauluun lisätyllä rivillä on aina uniikki pääavain. Kätevää!
  nimi varchar(100) NOT NULL, -- Muista erottaa sarakkeiden määrittelyt pilkulla!
  tiedot varchar(1000),
  salasana varchar(100) NOT NULL
);

CREATE TABLE Aanestys(
  id SERIAL PRIMARY KEY, -- SERIAL tyyppinen pääavain pitää huolen, että tauluun lisätyllä rivillä on aina uniikki pääavain. Kätevää!
  nimi varchar(100) NOT NULL, -- Muista erottaa sarakkeiden määrittelyt pilkulla!
  jarjestaja_id INTEGER REFERENCES Kayttaja(id),
  lisatieto varchar(1000),
  alkamisaika DATE NOT NULL,
  loppumisaika DATE,
  anonyymi BOOLEAN TRUE
);

CREATE TABLE Aanestajalista(
  kayttaja_id INTEGER REFERENCES Kayttaja(id),
  aanestys_id INTEGER REFERENCES Aanestys(id)
);

CREATE TABLE Ehdokas(
  id SERIAL PRIMARY KEY,
  nimi varchar(100) NOT NULL,
  lisatieto varchar(1000),
  aanestys_id INTEGER REFERENCES Aanestys(id)
);

CREATE TABLE Aani(
  id SERIAL PRIMARY KEY,
  ehdokas_id INTEGER REFERENCES Ehdokas(id),
  aanestys_id INTEGER REFERENCES Aanestys(id),
  aika TIMESTAMP NOW()
);