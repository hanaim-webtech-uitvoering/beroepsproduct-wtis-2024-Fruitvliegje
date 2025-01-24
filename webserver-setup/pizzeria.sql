IF OBJECT_ID('User', 'U') IS NOT NULL
    DROP TABLE [User];

IF OBJECT_ID('ProductType', 'U') IS NOT NULL
    DROP TABLE ProductType;

IF OBJECT_ID('Ingredient', 'U') IS NOT NULL
    DROP TABLE Ingredient;

IF OBJECT_ID('Product', 'U') IS NOT NULL
    DROP TABLE Product;

IF OBJECT_ID('Product_Ingredient', 'U') IS NOT NULL
    DROP TABLE Product_Ingredient;

IF OBJECT_ID('Pizza_Order_Product', 'U') IS NOT NULL
    DROP TABLE Pizza_Order_Product;

IF OBJECT_ID('Pizza_Order', 'U') IS NOT NULL
    DROP TABLE Pizza_Order;


-- Create User table
CREATE TABLE [User] (
  [username] NVARCHAR(255) PRIMARY KEY,
  [password] NVARCHAR(255) NOT NULL,
  [first_name] NVARCHAR(255) NOT NULL,
  [last_name] NVARCHAR(255) NOT NULL,
  [address] NVARCHAR(255),
  [role] NVARCHAR(50) NOT NULL
);

-- Create ProductType table
CREATE TABLE [ProductType] (
  [name] NVARCHAR(255) PRIMARY KEY
);

-- Create Ingredient table
CREATE TABLE [Ingredient] (
  [name] NVARCHAR(255) PRIMARY KEY
);

-- Create Product table
CREATE TABLE [Product] (
  [name] NVARCHAR(255) PRIMARY KEY,
  [price] DECIMAL(10,2) NOT NULL,
  [type_id] NVARCHAR(255) NOT NULL
);

-- Create Product_Ingredient table
CREATE TABLE [Product_Ingredient] (
  [product_name] NVARCHAR(255),
  [ingredient_name] NVARCHAR(255),
  PRIMARY KEY ([product_name], [ingredient_name])
);

CREATE TABLE [Pizza_Order] (
  [order_id] INT PRIMARY KEY IDENTITY(1, 1),
  [client_username] NVARCHAR(255),
  [client_name] NVARCHAR(255) NOT NULL,
  [personnel_username] NVARCHAR(255) NOT NULL,
  [datetime] DATETIME NOT NULL,
  [status] INT,
  [address] NVARCHAR(255)
)

-- Create Pizza_Order_Product table
CREATE TABLE [Pizza_Order_Product] (
  [order_id] INT,
  [product_name] NVARCHAR(255),
  [quantity] INT NOT NULL,
  PRIMARY KEY ([order_id], [product_name])
);

-- -- Add foreign key constraints
ALTER TABLE [Product] ADD FOREIGN KEY ([type_id]) REFERENCES [ProductType] ([name]);
ALTER TABLE [Product_Ingredient] ADD FOREIGN KEY ([product_name]) REFERENCES [Product] ([name]);
ALTER TABLE [Product_Ingredient] ADD FOREIGN KEY ([ingredient_name]) REFERENCES [Ingredient] ([name]);
ALTER TABLE [Pizza_Order] ADD FOREIGN KEY ([client_username]) REFERENCES [User] ([username]);
ALTER TABLE [Pizza_Order] ADD FOREIGN KEY ([personnel_username]) REFERENCES [User] ([username]);
ALTER TABLE [Pizza_Order_Product] ADD FOREIGN KEY ([order_id]) REFERENCES [Pizza_Order] ([order_id]);
ALTER TABLE [Pizza_Order_Product] ADD FOREIGN KEY ([product_name]) REFERENCES [Product] ([name]);

-- -- Insert statements for 20 users with realistic names
INSERT INTO [User] (username, [password], first_name, last_name, [role]) VALUES
('jdoe','8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'John', 'Doe', 'Client'),
('mvermeer', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Maria', 'Vermeer', 'Client'),
('rdeboer', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Rik', 'de Boer', 'Personnel'),
('sbakker', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Sophie', 'Bakker', 'Personnel'),
('fholwerda', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Fenna', 'Holwerda', 'Client'),
('kdijkstra', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Klaas', 'Dijkstra', 'Client'),
('lheineken', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Lucas', 'Heineken', 'Personnel'),
('mvandam', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Mila', 'van Dam', 'Personnel'),
('gkoolstra', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Gert', 'Koolstra', 'Client'),
('evisscher', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Emma', 'Visscher', 'Client'),
('tjanssen', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Tom', 'Janssen', 'Personnel'),
('abrouwer', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Anna', 'Brouwer', 'Personnel'),
('wbos', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Willem', 'Bos', 'Client'),
('tvandermeer', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Tessa', 'van der Meer', 'Client'),
('rkramer', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Rob', 'Kramer', 'Personnel'),
('mnijland', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Maud', 'Nijland', 'Personnel'),
('dschouten', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'David', 'Schouten', 'Client'),
('hdeleeuw', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Hanna', 'de Leeuw', 'Client'),
('pvanveen', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Peter', 'van Veen', 'Personnel'),
('adekhane', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Ahmed', 'Dekhane', 'Client'), 
('mbouaziz', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Mouna', 'Bouaziz', 'Client'), 
('tbayrak', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Tarik', 'Bayrak', 'Personnel'), 
('ayildiz', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Aylin', 'Yildiz', 'Personnel'), 
('rnarsingh', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Rajesh', 'Narsingh', 'Client'), 
('sdurga', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Shanti', 'Durga', 'Client'), 
('mkassem', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Mohammed', 'Kassem', 'Personnel'), 
('lsaleh', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Lina', 'Saleh', 'Personnel'), 
('aghebre', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Amanuel', 'Ghebre', 'Client'), 
('mtsega', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Miriam', 'Tsega', 'Client'), 
('pkowalski', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Piotr', 'Kowalski', 'Personnel'), 
('aivanov', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Alexei', 'Ivanov', 'Personnel'), 
('mkarimi', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Mina', 'Karimi', 'Client'), 
('hradman', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Hassan', 'Radman', 'Client'), 
('lbaloyi', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Lerato', 'Baloyi', 'Personnel'), 
('dpetrov', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Dmitri', 'Petrov', 'Personnel'), 
('ibrahimovic', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Ismail', 'Brahimovic', 'Client'), 
('snovak', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Sanja', 'Novak', 'Client'), 
('yabebe', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Yonas', 'Abebe', 'Personnel'), 
('ngebre', '8d969eef6ecad3c29a3a629280e686cf0fbdc3c125b1fcf61d9675c5a559ecdd', 'Nardos', 'Gebre', 'Personnel'); 

-- Insert statements for product types
INSERT INTO ProductType ([name]) VALUES
('Pizza'),
('Maaltijd'),
('Specerij'),
('Voorgerecht'),
('Drank');

-- Insert statements for ingredients
INSERT INTO Ingredient ([name]) VALUES
('Tomaat'),
('Kaas'), 
('Pepperoni'),
('Champignon'),
('Ui'),
('Sla'),
('Spek'),
('Saus');

-- Insert statements for products
INSERT INTO Product ([name], price, type_id) VALUES
('Margherita Pizza', 9.99, 'Pizza'),
('Pepperoni Pizza', 11.99, 'Pizza'),
('Vegetarische Pizza', 10.99, 'Pizza'),
('Hawaiian Pizza', 12.99, 'Pizza'),
('Combinatiemaaltijd', 15.99, 'Maaltijd'),
('Knoflookbrood', 4.99, 'Voorgerecht'),
('Coca Cola', 2.49, 'Drank'),
('Sprite', 2.49, 'Drank');

-- Insert statements for product-ingredient relationships
INSERT INTO Product_Ingredient (product_name, ingredient_name) VALUES
('Margherita Pizza', 'Tomaat'), -- Margherita Pizza met Tomaat
('Margherita Pizza', 'Kaas'), -- Margherita Pizza met Kaas
('Pepperoni Pizza', 'Tomaat'), -- Pepperoni Pizza met Tomaat
('Pepperoni Pizza', 'Kaas'), -- Pepperoni Pizza met Kaas
('Pepperoni Pizza', 'Pepperoni'), -- Pepperoni Pizza met Pepperoni
('Vegetarische Pizza', 'Tomaat'), -- Vegetarische Pizza met Tomaat
('Vegetarische Pizza', 'Kaas'), -- Vegetarische Pizza met Kaas
('Vegetarische Pizza', 'Champignon'), -- Vegetarische Pizza met Champignon
('Vegetarische Pizza', 'Ui'), -- Vegetarische Pizza met Ui
('Hawaiian Pizza', 'Tomaat'), -- Hawaiian Pizza met Tomaat
('Hawaiian Pizza', 'Kaas'), -- Hawaiian Pizza met Kaas
('Hawaiian Pizza', 'Pepperoni'), -- Hawaiian Pizza met Pepperoni
('Hawaiian Pizza', 'Ui'), -- Hawaiian Pizza met Ui
('Hawaiian Pizza', 'Sla'), -- Hawaiian Pizza met Sla
('Hawaiian Pizza', 'Spek'), -- Hawaiian Pizza met Spek
('Hawaiian Pizza', 'Saus'), -- Hawaiian Pizza met Saus
('Combinatiemaaltijd', 'Tomaat'), -- Combinatiemaaltijd met Tomaat
('Combinatiemaaltijd', 'Kaas'), -- Combinatiemaaltijd met Kaas
('Combinatiemaaltijd', 'Pepperoni'), -- Combinatiemaaltijd met Pepperoni
('Combinatiemaaltijd', 'Champignon'), -- Combinatiemaaltijd met Champignon
('Combinatiemaaltijd', 'Ui'), -- Combinatiemaaltijd met Ui
('Combinatiemaaltijd', 'Sla'), -- Combinatiemaaltijd met Sla
('Combinatiemaaltijd', 'Spek'), -- Combinatiemaaltijd met Spek
('Combinatiemaaltijd', 'Saus'); -- Combinatiemaaltijd met Saus

-- Insert statements for pizza orders
INSERT INTO [Pizza_Order] (client_username, client_name, personnel_username, datetime, status, address) VALUES
('jdoe', 'John Doe', 'rdeboer', '2024-06-12 18:45:00', 1, 'Bakkerstraat 1, 6811EG, Arnhem'),
('mvermeer', 'Maria Vermeer', 'sbakker', '2024-06-12 19:00:00', 2, 'Jansplein 2, 6811GD, Arnhem'),
('fholwerda', 'Fenna Holwerda', 'lheineken', '2024-06-12 19:15:00', 1, 'Willemsplein 3, 6811KD, Arnhem'),
('kdijkstra', 'Klaas Dijkstra', 'mvandam', '2024-06-12 19:30:00', 2, 'Kerkstraat 4, 6811DW, Arnhem'),
('gkoolstra', 'Gert Koolstra', 'tjanssen', '2024-06-12 19:45:00', 3, 'Rijnkade 5, 6811HA, Arnhem'),
(NULL, 'Pieter Post', 'abrouwer', '2024-06-12 20:00:00', 1, 'Grote Markt 6, 6511KB, Nijmegen'),
(NULL, 'Anna Smits', 'wbos', '2024-06-12 20:15:00', 2, 'Sint Annastraat 7, 6524EZ, Nijmegen'),
(NULL, 'Bert van Dijk', 'tvandermeer', '2024-06-12 20:30:00', 3, 'Oranjesingel 8, 6511NV, Nijmegen'),
(NULL, 'Sara de Vries', 'rkramer', '2024-06-12 20:45:00', 1, 'Van Welderenstraat 9, 6511MS, Nijmegen'),
(NULL, 'Jan Jansen', 'mnijland', '2024-06-12 21:00:00', 2, 'Molenstraat 10, 6511HJ, Nijmegen'),
('dschouten', 'David Schouten', 'hdeleeuw', '2024-06-13 18:45:00', 1, 'Velperweg 11, 6814AD, Arnhem'),
('evisscher', 'Emma Visscher', 'pvanveen', '2024-06-13 19:00:00', 2, 'Geitenkamp 12, 6815AP, Arnhem'),
('adekhane', 'Ahmed Dekhane', 'ayildiz', '2024-06-13 19:15:00', 1, 'IJssellaan 13, 6821DJ, Arnhem'),
('wbos', 'Willem Bos', 'tbayrak', '2024-06-13 19:30:00', 2, 'Broekstraat 14, 6822GD, Arnhem'),
('mnijland', 'Maud Nijland', 'mkassem', '2024-06-13 19:45:00', 3, 'Apeldoornsestraat 15, 6828AJ, Arnhem'),
(NULL, 'Els de Boer', 'lsaleh', '2024-06-13 20:00:00', 1, 'Marialaan 16, 6541RP, Nijmegen'),
(NULL, 'Tom Bakker', 'pkowalski', '2024-06-13 20:15:00', 2, 'Smetiusstraat 17, 6511EP, Nijmegen'),
(NULL, 'Mila Janssen', 'aivanov', '2024-06-13 20:30:00', 3, 'Van Oldenbarneveltstraat 18, 6511PA, Nijmegen'),
(NULL, 'Lars de Groot', 'mkarimi', '2024-06-13 20:45:00', 1, 'Hertogstraat 19, 6511RV, Nijmegen'),
(NULL, 'Rik Kramer', 'dpetrov', '2024-06-13 21:00:00', 2, 'Van Schaeck Mathonsingel 20, 6512AP, Nijmegen'),
(NULL, 'Sophie van der Meer', 'ibrahimovic', '2024-06-14 18:45:00', 1, 'Lange Hezelstraat 21, 6511CM, Nijmegen'),
('rdeboer', 'Rik de Boer', 'sbakker', '2024-06-14 19:00:00', 2, 'Waalkade 22, 6511XR, Nijmegen'),
('mvermeer', 'Maria Vermeer', 'lheineken', '2024-06-14 19:15:00', 1, 'Sint Jacobslaan 23, 6533BT, Nijmegen'),
('jdoe', 'John Doe', 'mvandam', '2024-06-14 19:30:00', 2, 'Van Broeckhuysenstraat 24, 6511PE, Nijmegen'),
(NULL, 'Henk de Wit', 'gkoolstra', '2024-06-14 19:45:00', 3, 'Ziekerstraat 25, 6511LH, Nijmegen');


-- Insert statements for Pizza_Order_Product (dummy data for orders)
INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) VALUES
(1, 'Margherita Pizza', 2),
(1, 'Coca Cola', 3),
(2, 'Pepperoni Pizza', 1),
(2, 'Sprite', 2),
(3, 'Vegetarische Pizza', 1),
(3, 'Hawaiian Pizza', 1),
(4, 'Combinatiemaaltijd', 2),
(4, 'Knoflookbrood', 1),
(5, 'Pepperoni Pizza', 1),
(6, 'Margherita Pizza', 3),
(6, 'Hawaiian Pizza', 2),
(7, 'Combinatiemaaltijd', 2),
(8, 'Knoflookbrood', 2),
(8, 'Sprite', 1),
(9, 'Pepperoni Pizza', 1),
(10, 'Hawaiian Pizza', 2),
(10, 'Coca Cola', 2),
(11, 'Margherita Pizza', 2),
(12, 'Vegetarische Pizza', 1),
(13, 'Hawaiian Pizza', 3),
(13, 'Coca Cola', 1),
(14, 'Combinatiemaaltijd', 1),
(14, 'Knoflookbrood', 1),
(15, 'Pepperoni Pizza', 2),
(15, 'Sprite', 2),
(16, 'Margherita Pizza', 1),
(17, 'Vegetarische Pizza', 2),
(18, 'Hawaiian Pizza', 1),
(19, 'Combinatiemaaltijd', 2),
(19, 'Knoflookbrood', 1),
(20, 'Pepperoni Pizza', 3),
(21, 'Hawaiian Pizza', 2),
(21, 'Coca Cola', 1),
(22, 'Margherita Pizza', 2),
(22, 'Knoflookbrood', 1),
(23, 'Pepperoni Pizza', 1),
(24, 'Vegetarische Pizza', 2),
(25, 'Hawaiian Pizza', 2),
(25, 'Sprite', 1);


-- pak de oudste en de nieuwste datum
declare @date_start datetime;
declare @date_end datetime;

select @date_start = MIN(datetime) from Pizza_Order;
select @date_end   = max(datetime) from Pizza_Order;

-- Bereken aan de hand van het verschil de middelste datum tussen start en eind
declare @diff int;
set @diff = DATEDIFF(minute, @date_start, @date_end);

declare @middle_date datetime;
set @middle_date = DATEADD(minute, @diff/2, @date_start);

-- Bereken verschil middelste datum met nu (huidig tijdstip)
set @diff = DATEDIFF(minute, @middle_date, GETDATE());

-- update vlucht vertrektijden en passagier inchecktijd
update Pizza_Order set [datetime] = DATEADD(minute, @diff, datetime);

go