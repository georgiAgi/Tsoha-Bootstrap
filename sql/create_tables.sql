CREATE TABLE Kayttaja(
  id SERIAL PRIMARY KEY,
  nimi varchar(100) NOT NULL,
  tiedot varchar(1000),
  salasana varchar(100) NOT NULL
);

CREATE TABLE Aanestys(
  id SERIAL PRIMARY KEY,
  nimi varchar(100) NOT NULL,
  jarjestaja_id INTEGER REFERENCES Kayttaja(id) ON DELETE CASCADE,
  lisatieto varchar(1000),
  alkamisaika DATE NOT NULL,
  loppumisaika DATE,
  anonyymi BOOLEAN,
  julkisettulokset INTEGER
);

CREATE TABLE Aanestajalista(
  kayttaja_id INTEGER REFERENCES Kayttaja(id) ON DELETE CASCADE,
  aanestys_id INTEGER REFERENCES Aanestys(id) ON DELETE CASCADE
);

CREATE TABLE Ehdokas(
  id SERIAL PRIMARY KEY,
  nimi varchar(100) NOT NULL,
  lisatieto varchar(1000),
  aanestys_id INTEGER REFERENCES Aanestys(id) ON DELETE CASCADE
);

CREATE TABLE Aani(
  id SERIAL PRIMARY KEY,
  ehdokas_id INTEGER REFERENCES Ehdokas(id) ON DELETE CASCADE,
  aika TIMESTAMP
);