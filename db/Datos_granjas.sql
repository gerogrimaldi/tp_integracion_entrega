
INSERT INTO usuarios (idUsuario, nombre, email, direccion, telefono, password, fechaNac, tipoUsuario) VALUES
(0, 'Brian', 'bngbrian@gmail.com', 'Los Talas 180', '+54934345555', '$2y$10$87AuVnPR/6KMWJua9KwBJeAqIAPWeUSr0FPtmWQ9lsqZ4ywxqy4ny', '2000-03-01', 'Encargado'),
(1, 'Nahuel', 'nahuel@gmail.com', 'Los Colibries 1130', '+54933345555', '$2y$10$87AuVnPR/6KMWJua9KwBJeAqIAPWeUSr0FPtmWQ9lsqZ4ywxqy4ny', '1998-03-01', 'Propietario');

INSERT INTO granja (idGranja, nombre, habilitacionSenasa, metrosCuadrados, ubicacion) VALUES
(0, 'Granja la chorlita', '07-892-0467', '80', 'Aldea San Rafael'),
(1, 'Granja el nieto', '10-015-8905', '120', 'Aldea San Juan'),
(2, 'Avicola Maria Clara', '07-012-0405', '120', 'Aldea Santa Rosa');

INSERT INTO tipoAve (idTipoAve, nombre) VALUES
(0, 'Ponedora blanca'),
(1, 'Ponedora Sussex'),
(2, 'Ponedora Roja');

INSERT INTO galpon (idGalpon, identificacion, idTipoAve, capacidad, idGranja) VALUES
(0, '001-Frente', '0', '60000', '1'),
(1, '002-Fondo', '1', '120000', '1'),
(2, '003-Medio', '1', '40000', '1');

INSERT INTO tipoMantenimiento (idTipoMantenimiento, nombre) VALUES
(0, 'Corte de cesped'),
(1, 'Fumigacien contra plagas'),
(2, 'Colocacien de cebos para roedores');

INSERT INTO mantenimientoGranja (idMantenimientoGranja, fecha, idGranja, idTipoMantenimiento) VALUES
(0, '2025-01-22', '0', '0'),
(1, '2025-01-01', '1', '1'),
(2, '2025-01-20', '1', '2');

INSERT INTO mantenimientoGalpon (idMantenimientoGalpon, fecha, idGalpon, idTipoMantenimiento) VALUES
(0, '2025-01-22', '0', '0'),
(1, '2025-01-01', '0', '1'),
(2, '2025-01-20', '1', '2');

INSERT INTO viaAplicacion (idViaAplicacion, via) VALUES
(0, 'Subcutánea'),
(1, 'Intramuscular'),
(2, 'Alas'),
(3, 'Spray'),
(4, 'En agua'),
(5, 'En alimento'),
(6, 'Ocular'),
(7, 'Nasal');

INSERT INTO vacuna (idVacuna, nombre, idViaAplicacion, marca, enfermedad) VALUES
(0, 'Pfizer', '0', 'Sun Microvirus', 'Gripe aviar'),
(1, 'Covid-19', '1', 'Avast', 'Influenza aviar'),
(2, 'Antitetanica', '1', 'AVG', 'Viruela aviar');

INSERT INTO loteVacuna (idLoteVacuna, numeroLote, fechaCompra, cantidad, vencimiento, idVacuna) VALUES
(0, '00123-5482', '2025-03-01', '5200', '2028-01-01', '2'),
(1, '129-2025', '2025-01-25', '3560', '2028-02-01', '1'),
(2, 'A12025', '2024-09-11','20000', '2028-03-01', '0');

INSERT INTO loteAves (idLoteAves, identificador, fechaNacimiento, fechaCompra, cantidadAves, idTipoAve, precioCompra) VALUES
(0, 'L001-2025', '2024-12-15', '2025-01-10', 5000, 0, '200'), -- Ponedora blanca
(1, 'L002-2025', '2025-01-01', '2025-01-20', 8000, 1, '250'), -- Ponedora Sussex
(2, 'L003-2025', '2025-02-01', '2025-02-15', 12000, 2, '100'); -- Ponedora Roja

INSERT INTO galpon_loteAves (idGalpon_loteAve, idLoteAves, idGalpon, fechaInicio, fechaFin) VALUES
(0, 0, 0, '2025-01-11', NULL), -- Lote 0 en galpón 001-Frente - granja 1
(1, 1, 1, '2025-01-21', NULL), -- Lote 1 en galpón 002-Fondo - granja 1
(2, 2, 2, '2025-02-16', NULL); -- Lote 2 en galpón 003-Medio - granja 1