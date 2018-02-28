-- Users
CREATE TABLE users (
    email VARCHAR(32),
    userid INTEGER,
    username VARCHAR(32) unique not null,
    pwd VARCHAR(32) not null,
    phone numeric(8, 0) not null,
    gender VARCHAR(6),
    bday DATE,
    driverLicense VARCHAR(9),
    primary key(email, userid),
    check(email like '___%@%___.com' and username not like '%[0-9]%')
);

-- Rides
CREATE TABLE rides (
    rideid INTEGER primary key,
    dates DATE not null,
    times TIME not null,
    origin VARCHAR(32) not null,
    destination VARCHAR(32) not null,
    basePrice INTEGER not null,
    capacity INTEGER not null,
    biddingType VARCHAR(10) not null,
    sidenote TEXT
);

-- Cars
CREATE TABLE cars (
    carid INTEGER,
    licensePlate VARCHAR(8),
    carType VARCHAR(32) not null,
    primary key(carid, licensePlate),
    check(licensePlate like '[A-Z][A-Z][A-Z][0-9][0-9][0-9][0-9][A-Z]')
);

-- Admins
CREATE TABLE admins (
    adminid INTEGER,
    adminname VARCHAR(32),
    adminpwd VARCHAR(32) not null,
    employeename VARCHAR(32) not null
    primary key(adminid, adminname)
);

-- User owns cars
CREATE TABLE owns (
    userid INTEGER,
    carid INTEGER,
    primary key(userid, carid)
);

-- User drives rides
CREATE TABLE drives (
    userid INTEGER,
    dates DATE,
    times TIME,
    rideid INTEGER unique not null,
    carid INTEGER not null,
    primary key(userid, dates, times)
);

-- User bids for rides
CREATE TABLE bids (
    userid INTEGER,
    rideid INTEGER,
    price INTEGER not null,
    status VARCHAR(1) not null,
    sidenote TEXT,
    primary key(userid, rideid)
);

-- Administrator manages entities
CREATE TABLE manages (
    adminid INTEGER not null,
    managetype VARCHAR(6) not null,
    typeid INTEGER not null,
    history VARCHAR(32) not null
);