CREATE TABLE tx_cabaggeolocate_territory (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    title varchar(30) DEFAULT '' NOT NULL,
    country_initial varchar(4) DEFAULT '' NOT NULL,
    country varchar(4) DEFAULT '' NOT NULL,
    domains tinytext,
    coordinates text,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_cabaggeolocate_cache (
  ip varchar(15) NOT NULL,
  territoryID int(11) NOT NULL,
  territory varchar(50) NOT NULL,
  country varchar(20) NOT NULL,
  timeout int(11) NOT NULL,

  PRIMARY KEY  (`ip`)
);
