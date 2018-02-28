-- Users
INSERT INTO users
values('renee@gmail.com', 123456789, 'Renee', 123456, 81234567, 'Female', '1997-06-01', 'S1234567A');
INSERT INTO users
values('yilun@gmail.com', 234567891, 'Yilun', 234567, 81234569, 'Male', '1997-06-02', 'S1234567B');
INSERT INTO users
values('brian@gmail.com', 345678912, 'Brian', 345678, 81234561, 'Male', '1997-06-03', 'S1234567C');
INSERT INTO users
values('cindy@gmail.com', 456789123, 'Cindy', 456789, 81234562, 'Female', '1997-06-04', 'S1234567D');
INSERT INTO users
values('billy@gmail.com', 567891234, 'Billy', 567891, 81234563, 'Male', '1997-06-05', null);
INSERT INTO users
values('peggy@gmail.com', 678912345, 'Peggy', 678912, 81234564, 'Female', null, null);
INSERT INTO users
values('alex@gmail.com', 789123456, 'Alex', 789123, 81234565, null, null, null);
INSERT INTO users
values('anna@gmail.com', 891234567, 'Anna', 891234, 81234566, 'Female', null, 'S1234567E');
INSERT INTO users
values('martin@gmail.com', 912345678, 'Martin', 912345, 81234568, null, '1997-06-06', 'S1234567F');

-- Rides
INSERT INTO rides
values(12345678, '2018-02-28', '14:20:20', 'Tampines MRT', 'Changi Airport', 10, 3, 'Auto', 'Only waiting for 10 minutes');
INSERT INTO rides
values(23456781, '2018-02-28', '15:20:20', 'Tampines MRT', 'Bishan MRT', 30, 2, 'Auto', null);
INSERT INTO rides
values(34567812, '2018-02-28', '09:00:20', 'NUS School of Computing', 'Bishan MRT', 25, 2, 'Self', null);
INSERT INTO rides
values(45678123, '2018-02-28', '09:05:20', 'Botanic Gardens MRT', 'Bishan MRT', 15, 5, 'Auto', null);
INSERT INTO rides
values(56781234, '2018-03-01', '09:30:00', 'Clementi MRT', 'Ang Mo Kio MRT', 24, 3, 'Self', 'Waiting for 5 minutes');
INSERT INTO rides
values(67812345, '2018-03-03', '22:05:08', 'Crescent Girls School', 'Orchard MRT', 10, 5, 'Auto', null);

-- Cars
INSERT INTO cars
values(1234567, 'SGD1234A', 'Black SUV');
INSERT INTO cars
values(2345671, 'SGD1234B', 'White Honda');
INSERT INTO cars
values(3456712, 'SGD1234C', 'Blue Audi');
INSERT INTO cars
values(4567123, 'SGD1234D', 'Silver Toyota');
INSERT INTO cars
values(5671234, 'SGD1234E', 'Red Lexus');
INSERT INTO cars
values(6712345, 'SGD1234F', 'Dark Blue Ford');
INSERT INTO cars
values(7123456, 'SGD1234G', 'White Nissan');

-- Admins
INSERT INTO admins
values(1234, 'admin1', 2280, 'Renee');
INSERT INTO admins
values(2341, 'admin2', 1198, 'Yilun');
INSERT INTO admins
values(3412, 'admin3', 8739, 'Brian');
INSERT INTO admins
values(4123, 'admin4', 9932, 'Cindy');

-- User owns cars
INSERT INTO owns
values(123456789, 1234567);
INSERT INTO owns
values(123456789, 7123456);
INSERT INTO owns
values(234567891, 2345671);
INSERT INTO owns
values(345678912, 3456712);
INSERT INTO owns
values(456789123, 4567123);
INSERT INTO owns
values(891234567, 5671234);
INSERT INTO owns
values(912345678, 6712345);

-- User drives rides
INSERT INTO drives
values(123456789, 12345678, 1234567);
INSERT INTO drives
values(123456789, 23456781, 1234567);
INSERT INTO drives
values(234567891, 34567812, 2345671);
INSERT INTO drives
values(891234567, 45678123, 5671234);
INSERT INTO drives
values(912345678, 56781234, 6712345);
INSERT INTO drives
values(456789123, 67812345, 4567123);

-- User bids for rides
INSERT INTO bids
values(567891234, 12345678, 12, 0, null);
INSERT INTO bids
values(789123456, 12345678, 13, 0, null);
INSERT INTO bids
values(912345678, 56781234, 24, 0, null);
INSERT INTO bids
values(891234567, 67812345, 15, 0, null);
INSERT INTO bids
values(345678912, 34567812, 26, 0, null);
INSERT INTO bids
values(789123456, 45678123, 15, 0, null);

-- Administrator manages entities
INSERT INTO manages
values(1234, 'Rides', '34567812', 'Modify comments');