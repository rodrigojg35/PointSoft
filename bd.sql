-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2024 a las 20:34:36
-- Versión del servidor: 8.0.34
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sao`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `cerrarCaja` (IN `fecha_param` VARCHAR(8))   BEGIN
    UPDATE caja
    SET hrcierre_c = fecha_param
    WHERE hrcierre_c IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarPreventa` (IN `p_prod_pr` INT, IN `p_cant_pr` INT, IN `p_preciou_pr` DOUBLE, IN `p_abono_pr` DOUBLE, IN `p_tipopago_pr` CHAR(1), IN `p_fecha_pr` DATE, IN `p_emp_pr` INT, IN `p_estado_pr` CHAR(1), IN `p_nombre_pr` CHAR(80), IN `p_tel_pr` DOUBLE, IN `p_email_pr` CHAR(80), IN `p_hra_pr` TIME)   BEGIN
	DECLARE last_id INT;
    
    -- Insertar la nueva fila
    INSERT INTO preventas(prod_pr, cant_pr, preciou_pr, abono_pr, tipopago_pr, 
	fecha_pr, emp_pr, estado_pr, nombre_pr, tel_pr, email_pr, hra_pr) 
	VALUES(p_prod_pr, p_cant_pr, p_preciou_pr, p_abono_pr, p_tipopago_pr, 
	p_fecha_pr, p_emp_pr, p_estado_pr, p_nombre_pr, p_tel_pr, p_email_pr, p_hra_pr);

    -- Obtener el último ID insertado
    SET last_id = LAST_INSERT_ID();

    -- Actualizar el valor de img_pr
    UPDATE preventas
    SET img_pr = CONCAT('comprobantes/preventas/preventa_num', last_id, '.pdf')
    WHERE id_pr = last_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarVenta` (IN `v_total_v` DOUBLE, IN `v_tipopago_v` CHAR(1), IN `v_fecha_v` DATE, IN `v_emp_v` INT, IN `v_prev_v` INT, IN `v_nombre_v` CHAR(80), IN `v_tel_v` DOUBLE, IN `v_email_v` CHAR(80), IN `v_hra_v` TIME)   BEGIN
	DECLARE last_id INT;
    
    -- Insertar la nueva fila
    INSERT INTO ventas(total_v, tipopago_v, fecha_v, emp_v, prev_v, nombre_v, tel_v, email_v, hra_v) 
	VALUES(v_total_v, v_tipopago_v, v_fecha_v, v_emp_v, v_prev_v, v_nombre_v, v_tel_v, v_email_v, v_hra_v);

    -- Obtener el último ID insertado
    SET last_id = LAST_INSERT_ID();

    -- Actualizar el valor de img_pr
    UPDATE ventas
    SET img_v = CONCAT('comprobantes/ventas/venta_num', last_id, '.pdf')
    WHERE id_v = last_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerTotalTransacciones` (IN `emp_id_param` INT, OUT `total` INT)   BEGIN
    SELECT 
        (SELECT COUNT(*) FROM preventas WHERE emp_pr = emp_id_param) +
        (SELECT COUNT(*) FROM ventas WHERE emp_v = emp_id_param) +
        (SELECT COUNT(*) FROM caja WHERE emp_c = emp_id_param) 
    INTO total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `id_c` int NOT NULL,
  `fecha_c` date NOT NULL,
  `hrabrir_c` time NOT NULL,
  `hrcierre_c` time DEFAULT NULL,
  `cantventas_c` int DEFAULT NULL,
  `totale_c` int DEFAULT NULL,
  `totalt_c` int DEFAULT NULL,
  `emp_c` int NOT NULL,
  `img_c` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`id_c`, `fecha_c`, `hrabrir_c`, `hrcierre_c`, `cantventas_c`, `totale_c`, `totalt_c`, `emp_c`, `img_c`) VALUES
(1, '2050-12-05', '21:29:40', '21:30:41', 0, 0, 0, 2, NULL),
(87, '2024-06-03', '22:44:12', '23:17:00', 2, 1722, 0, 2, 'comprobantes/caja/caja_num87.pdf'),
(88, '2024-06-03', '23:21:02', '23:21:00', 0, 0, 0, 2, 'comprobantes/caja/caja_num88.pdf'),
(89, '2024-06-04', '02:01:37', '03:16:00', 6, 14297, 111, 2, 'comprobantes/caja/caja_num89.pdf'),
(90, '2024-06-04', '03:18:36', NULL, 1, 900, 0, 2, 'comprobantes/caja/caja_num90.pdf');

--
-- Disparadores `caja`
--
DELIMITER $$
CREATE TRIGGER `asignarTicketCaja` BEFORE UPDATE ON `caja` FOR EACH ROW BEGIN
    SET new.img_c = CONCAT('comprobantes/caja/caja_num', new.id_c, '.pdf');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listado_preventas_select`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `listado_preventas_select` (
`abono_pr` double
,`cant_pr` int
,`id_pr` int
,`nom_p` char(100)
,`nombre_pr` char(80)
,`preciou_pr` double
,`prod_pr` int
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_venta`
--

CREATE TABLE `lista_venta` (
  `id_lv` int NOT NULL,
  `prod_lv` int NOT NULL,
  `nom_lv` char(100) NOT NULL,
  `cant_lv` int NOT NULL,
  `preciou_lv` double NOT NULL,
  `venta_lv` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `lista_venta`
--

INSERT INTO `lista_venta` (`id_lv`, `prod_lv`, `nom_lv`, `cant_lv`, `preciou_lv`, `venta_lv`) VALUES
(177, 28, 'Resident Evil 2', 3, 500, 113),
(178, 22, 'Grand Theft Auto VI', 1, 1000, 114),
(179, 20, 'Diablo II', 2, 600, 115),
(180, 23, 'Far Cry', 3, 299, 115),
(181, 21, 'Dragon Ball Z', 1, 300, 116),
(182, 24, 'Spider-Man: Remastered', 12, 900, 116),
(183, 24, 'Spider-Man: Remastered', 1, 900, 117);

--
-- Disparadores `lista_venta`
--
DELIMITER $$
CREATE TRIGGER `asignarVentaALista` BEFORE INSERT ON `lista_venta` FOR EACH ROW BEGIN
	IF new.venta_lv IS NULL THEN
        -- Almacenar el último id_v generado en la tabla ventas en una variable
        SET @ultimo_id_v := (SELECT id_v FROM ventas ORDER BY id_v DESC LIMIT 1);
        
        -- Asignar el valor de la variable a venta_lv
        SET new.venta_lv = @ultimo_id_v;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restarStockVentas` AFTER INSERT ON `lista_venta` FOR EACH ROW BEGIN
    IF (NEW.cant_lv < (SELECT cant_p FROM productos WHERE id_p = NEW.prod_lv)) THEN
        UPDATE productos
        SET cant_p = cant_p - NEW.cant_lv
        WHERE id_p = NEW.prod_lv;
    ELSE
        UPDATE productos
        SET cant_p = cant_p - NEW.cant_lv,
            estado_p = "N"
        WHERE id_p = NEW.prod_lv;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preventas`
--

CREATE TABLE `preventas` (
  `id_pr` int NOT NULL,
  `caja_pr` int NOT NULL,
  `prod_pr` int NOT NULL,
  `cant_pr` int NOT NULL,
  `preciou_pr` double NOT NULL,
  `abono_pr` double DEFAULT NULL,
  `tipopago_pr` char(1) NOT NULL,
  `fecha_pr` date NOT NULL,
  `emp_pr` int NOT NULL,
  `estado_pr` char(1) NOT NULL,
  `nombre_pr` char(80) NOT NULL,
  `tel_pr` double DEFAULT NULL,
  `email_pr` char(80) NOT NULL,
  `hra_pr` time DEFAULT NULL,
  `img_pr` varchar(255) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `preventas`
--

INSERT INTO `preventas` (`id_pr`, `caja_pr`, `prod_pr`, `cant_pr`, `preciou_pr`, `abono_pr`, `tipopago_pr`, `fecha_pr`, `emp_pr`, `estado_pr`, `nombre_pr`, `tel_pr`, `email_pr`, `hra_pr`, `img_pr`) VALUES
(1, 1, 1, 0, 0, 0, 'C', '2023-05-05', 1, 'L', 'NO', 2292647029, 'rojg', NULL, NULL),
(85, 87, 22, 1, 1000, 222, 'C', '2024-06-03', 2, 'S', 'Rodrigo Jurado González', 2292647029, 'fr.quintero@outlook.com', '22:44:40', 'comprobantes/preventas/preventa_num85.pdf'),
(86, 89, 22, 5, 1000, 100, 'C', '2024-06-04', 2, 'L', 'Rodrigo Jurado González', 229264702, 'Franciscomx586@gmail.com', '02:57:12', 'comprobantes/preventas/preventa_num86.pdf'),
(87, 89, 26, 2, 1500, 222, 'C', '2024-06-04', 2, 'L', 'Rodrigo Jurado González', 2292647023, 'arreta31@gmail.com', '02:58:19', 'comprobantes/preventas/preventa_num87.pdf'),
(88, 89, 22, 15, 1000, 111, 'T', '2024-06-04', 2, 'L', 'Rodrigo Jurado González', 2292647029, 'Franciscomx586@gmail.com', '03:11:13', 'comprobantes/preventas/preventa_num88.pdf');

--
-- Disparadores `preventas`
--
DELIMITER $$
CREATE TRIGGER `asignarACaja` BEFORE INSERT ON `preventas` FOR EACH ROW BEGIN
	IF new.caja_pr IS NULL THEN
		SET new.caja_pr = (SELECT id_c FROM caja WHERE hrcierre_c IS NULL LIMIT 1);
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restarStockPreventas` AFTER INSERT ON `preventas` FOR EACH ROW BEGIN
    IF (NEW.cant_pr < (SELECT cant_p FROM productos WHERE id_p = NEW.prod_pr)) THEN
        UPDATE productos
        SET cant_p = cant_p - NEW.cant_pr
        WHERE id_p = NEW.prod_pr;
    ELSE
        UPDATE productos
        SET cant_p = cant_p - NEW.cant_pr,
            preventa_p = "N"
        WHERE id_p = NEW.prod_pr;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumarACaja` AFTER INSERT ON `preventas` FOR EACH ROW BEGIN
	UPDATE caja SET cantventas_c = cantventas_c + 1 WHERE hrcierre_c IS NULL LIMIT 1;
	IF new.tipopago_pr = 'C' THEN
		UPDATE caja SET totale_c = totale_c + new.abono_pr WHERE hrcierre_c IS NULL LIMIT 1;
	END IF;
    IF new.tipopago_pr = 'T' THEN
		UPDATE caja SET totalt_c = totalt_c + new.abono_pr WHERE hrcierre_c IS NULL LIMIT 1;
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `preventasdeldia`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `preventasdeldia` (
`caja` int
,`cierre` time
,`hora` time
,`idtrans` int
,`tipopago` varchar(13)
,`Total` double
,`usuario` char(30)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `preventasultcaja`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `preventasultcaja` (
`caja` int
,`hora` time
,`idtrans` int
,`tipopago` varchar(13)
,`Total` double
,`usuario` char(30)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_p` int NOT NULL,
  `nom_p` char(100) DEFAULT NULL,
  `cant_p` int NOT NULL,
  `preciou_p` double NOT NULL,
  `estado_p` char(1) NOT NULL,
  `preventa_p` char(1) NOT NULL,
  `img_p` varchar(255) DEFAULT NULL,
  `img_pr` varchar(255) DEFAULT NULL,
  `descr_p` char(200) DEFAULT 'Descripcion generica de producto'
) ;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_p`, `nom_p`, `cant_p`, `preciou_p`, `estado_p`, `preventa_p`, `img_p`, `img_pr`, `descr_p`) VALUES
(1, 'NON', 15, 1492, 'D', 'N', NULL, NULL, 'Descripcion generica de producto'),
(20, 'Diablo II', 76, 600, 'D', 'N', 'diablo2.jpg', NULL, 'Descripcion de Diablo'),
(21, 'Dragon Ball Z', 0, 300, 'N', 'N', 'Dragon ball Z.jpg', NULL, ''),
(22, 'Grand Theft Auto VI', 3, 1000, 'N', 'S', 'Grand_Theft_Auto_VI.png', NULL, ''),
(23, 'Far Cry', 57, 299, 'D', 'N', 'far-cry.jpg', NULL, ''),
(24, 'Spider-Man: Remastered', 2, 900, 'D', 'N', '1687885039-marvels-spider-man-remastered-ps5-0.jpg', NULL, 'Spiderman The game'),
(25, 'Tomb Raider DE', 26, 1100, 'D', 'N', 'tomb raider.jpg', NULL, 'Tomb raider Definitive Edition'),
(26, 'The Last of Us II', 13, 1500, 'N', 'S', '1571760728-the-last-of-us-part-ii-ps4.png', NULL, 'The last of us part II'),
(28, 'Resident Evil 2', 14, 500, 'D', 'N', 'resident evil 2.jpg', NULL, 'Resident Evil 2'),
(29, 'Destiny', 15, 900, 'D', 'N', 'Destiniy.jpg', NULL, 'Destiny');

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `actualizarEstado` BEFORE UPDATE ON `productos` FOR EACH ROW BEGIN
    IF NEW.cant_p > 0 AND NEW.preventa_p = 'N' THEN
        SET NEW.estado_p = 'D';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `transaccionesdeldia`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `transaccionesdeldia` (
`hora` time
,`idtrans` int
,`tipo` varchar(8)
,`tipopago` varchar(13)
,`Total` double
,`usuario` char(30)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `transaccionesdeultcaja`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `transaccionesdeultcaja` (
`hora` time
,`idtrans` int
,`tipo` varchar(8)
,`tipopago` varchar(13)
,`Total` double
,`usuario` char(30)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_u` int NOT NULL,
  `user_u` char(30) NOT NULL,
  `pass_u` char(30) NOT NULL,
  `tipo_u` char(8) NOT NULL,
  `tel_u` double DEFAULT NULL,
  `email_u` char(70) DEFAULT NULL,
  `estado_u` char(1) NOT NULL
) ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_u`, `user_u`, `pass_u`, `tipo_u`, `tel_u`, `email_u`, `estado_u`) VALUES
(1, 'admin', '12345', 'admin', 2292647029, 'ro.jg01@hotmail.com', 'A'),
(2, 'Fati', '12345', 'emp', 2292647029, 'ro.jg01@hotmail.com', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_v` int NOT NULL,
  `caja_v` int NOT NULL,
  `total_v` double DEFAULT NULL,
  `tipopago_v` char(1) NOT NULL,
  `fecha_v` date NOT NULL,
  `emp_v` int NOT NULL,
  `prev_v` int NOT NULL,
  `nombre_v` char(80) NOT NULL,
  `tel_v` double DEFAULT NULL,
  `email_v` char(80) NOT NULL,
  `hra_v` time DEFAULT NULL,
  `img_v` varchar(255) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_v`, `caja_v`, `total_v`, `tipopago_v`, `fecha_v`, `emp_v`, `prev_v`, `nombre_v`, `tel_v`, `email_v`, `hra_v`, `img_v`) VALUES
(113, 87, 1500, 'C', '2024-06-03', 2, 1, 'Rodrigo Jurado González', 2292647029, 'Franciscomx586@gmail.com', '23:10:12', 'comprobantes/ventas/venta_num113.pdf'),
(114, 89, 778, 'C', '2024-06-04', 2, 85, 'Rodrigo Jurado González', 2292647022, 'ro.jg01@hotmail.com', '02:33:41', 'comprobantes/ventas/venta_num114.pdf'),
(115, 89, 2097, 'C', '2024-06-04', 2, 1, 'Rodrigo Jurado González', 2292647022, 'ro.jg01@hotmail.com', '03:02:49', 'comprobantes/ventas/venta_num115.pdf'),
(116, 89, 11100, 'C', '2024-06-04', 2, 1, 'Rodrigo Jurado González', 2292647022, 'ro.jg01@hotmail.com', '03:15:40', 'comprobantes/ventas/venta_num116.pdf'),
(117, 90, 900, 'C', '2024-06-04', 2, 1, 'Rodrigo Jurado González', 229264702, 'fr.quintero@outlook.com', '03:18:47', 'comprobantes/ventas/venta_num117.pdf');

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `asignarACaja2` BEFORE INSERT ON `ventas` FOR EACH ROW BEGIN
	IF new.caja_v IS NULL THEN
		SET new.caja_v = (SELECT id_c FROM caja WHERE hrcierre_c IS NULL LIMIT 1);
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `cambiarEstadoPreventa` AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
	IF new.prev_v != 1 THEN
        UPDATE preventas SET estado_pr = 'S' WHERE id_pr = new.prev_v;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumarACaja2` AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
	UPDATE caja SET cantventas_c = cantventas_c + 1 WHERE hrcierre_c IS NULL LIMIT 1;
	IF new.tipopago_v = 'C' THEN
		UPDATE caja SET totale_c = totale_c + new.total_v WHERE hrcierre_c IS NULL LIMIT 1;
	END IF;
    IF new.tipopago_v = 'T' THEN
		UPDATE caja SET totalt_c = totalt_c + new.total_v WHERE hrcierre_c IS NULL LIMIT 1;
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `ventasdeldia`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `ventasdeldia` (
`caja` int
,`cierre` time
,`hora` time
,`idtrans` int
,`tipopago` varchar(13)
,`Total` double
,`usuario` char(30)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `ventasultcaja`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `ventasultcaja` (
`caja` int
,`hora` time
,`idtrans` int
,`tipopago` varchar(13)
,`Total` double
,`usuario` char(30)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_con_transacciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_con_transacciones` (
`cant_p` int
,`estado_p` char(1)
,`id_p` int
,`img_p` varchar(255)
,`nom_p` char(100)
,`preciou_p` double
,`preventa_p` char(1)
,`total_transacciones` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_usuarios_con_transacciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_usuarios_con_transacciones` (
`email_u` char(70)
,`estado_u` char(1)
,`id_u` int
,`tel_u` double
,`tipo_u` char(8)
,`total_transacciones` bigint
,`user_u` char(30)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `listado_preventas_select`
--
DROP TABLE IF EXISTS `listado_preventas_select`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listado_preventas_select`  AS SELECT `preventas`.`id_pr` AS `id_pr`, `preventas`.`prod_pr` AS `prod_pr`, `preventas`.`cant_pr` AS `cant_pr`, `preventas`.`preciou_pr` AS `preciou_pr`, `productos`.`nom_p` AS `nom_p`, `preventas`.`abono_pr` AS `abono_pr`, `preventas`.`nombre_pr` AS `nombre_pr` FROM (`preventas` join `productos`) WHERE ((`preventas`.`prod_pr` = `productos`.`id_p`) AND (`preventas`.`id_pr` > 1) AND (`preventas`.`estado_pr` = 'L')) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `preventasdeldia`
--
DROP TABLE IF EXISTS `preventasdeldia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `preventasdeldia`  AS SELECT `preventasdeldia`.`idtrans` AS `idtrans`, `preventasdeldia`.`usuario` AS `usuario`, `preventasdeldia`.`hora` AS `hora`, `preventasdeldia`.`Total` AS `Total`, `preventasdeldia`.`tipopago` AS `tipopago`, `preventasdeldia`.`caja` AS `caja`, `preventasdeldia`.`cierre` AS `cierre` FROM (select `preventas`.`id_pr` AS `idtrans`,`usuarios`.`user_u` AS `usuario`,`preventas`.`hra_pr` AS `hora`,`preventas`.`abono_pr` AS `Total`,(case when (`preventas`.`tipopago_pr` = 'C') then 'Efectivo' when (`preventas`.`tipopago_pr` = 'T') then 'Transferencia' else 'Otro' end) AS `tipopago`,`preventas`.`caja_pr` AS `caja`,`caja`.`hrcierre_c` AS `cierre` from ((`preventas` join `usuarios`) join `caja`) where ((`preventas`.`emp_pr` = `usuarios`.`id_u`) and (`preventas`.`caja_pr` = `caja`.`id_c`) and (`preventas`.`id_pr` <> 1))) AS `preventasdeldia` WHERE (`preventasdeldia`.`cierre` is null) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `preventasultcaja`
--
DROP TABLE IF EXISTS `preventasultcaja`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `preventasultcaja`  AS SELECT `preventasdeldia`.`idtrans` AS `idtrans`, `preventasdeldia`.`usuario` AS `usuario`, `preventasdeldia`.`hora` AS `hora`, `preventasdeldia`.`Total` AS `Total`, `preventasdeldia`.`tipopago` AS `tipopago`, `preventasdeldia`.`caja` AS `caja` FROM (select `preventas`.`id_pr` AS `idtrans`,`usuarios`.`user_u` AS `usuario`,`preventas`.`hra_pr` AS `hora`,`preventas`.`abono_pr` AS `Total`,(case when (`preventas`.`tipopago_pr` = 'C') then 'Efectivo' when (`preventas`.`tipopago_pr` = 'T') then 'Transferencia' else 'Otro' end) AS `tipopago`,`preventas`.`caja_pr` AS `caja` from ((`preventas` join `usuarios`) join `caja`) where ((`preventas`.`emp_pr` = `usuarios`.`id_u`) and (`preventas`.`caja_pr` = `caja`.`id_c`) and (`preventas`.`id_pr` <> 1))) AS `preventasdeldia` WHERE (`preventasdeldia`.`caja` = (select max(`caja`.`id_c`) from `caja` limit 1)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `transaccionesdeldia`
--
DROP TABLE IF EXISTS `transaccionesdeldia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `transaccionesdeldia`  AS SELECT `preventasdeldia`.`idtrans` AS `idtrans`, 'Preventa' AS `tipo`, `preventasdeldia`.`usuario` AS `usuario`, `preventasdeldia`.`hora` AS `hora`, `preventasdeldia`.`Total` AS `Total`, `preventasdeldia`.`tipopago` AS `tipopago` FROM `preventasdeldia`union select `ventasdeldia`.`idtrans` AS `idtrans`,'Venta' AS `tipo`,`ventasdeldia`.`usuario` AS `usuario`,`ventasdeldia`.`hora` AS `hora`,`ventasdeldia`.`Total` AS `Total`,`ventasdeldia`.`tipopago` AS `tipopago` from `ventasdeldia` order by `hora` desc  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `transaccionesdeultcaja`
--
DROP TABLE IF EXISTS `transaccionesdeultcaja`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `transaccionesdeultcaja`  AS SELECT `preventasultcaja`.`idtrans` AS `idtrans`, 'Preventa' AS `tipo`, `preventasultcaja`.`usuario` AS `usuario`, `preventasultcaja`.`hora` AS `hora`, `preventasultcaja`.`Total` AS `Total`, `preventasultcaja`.`tipopago` AS `tipopago` FROM `preventasultcaja`union select `ventasultcaja`.`idtrans` AS `idtrans`,'Venta' AS `tipo`,`ventasultcaja`.`usuario` AS `usuario`,`ventasultcaja`.`hora` AS `hora`,`ventasultcaja`.`Total` AS `Total`,`ventasultcaja`.`tipopago` AS `tipopago` from `ventasultcaja` order by `hora` desc  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `ventasdeldia`
--
DROP TABLE IF EXISTS `ventasdeldia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ventasdeldia`  AS SELECT `ventasdeldia`.`idtrans` AS `idtrans`, `ventasdeldia`.`usuario` AS `usuario`, `ventasdeldia`.`hora` AS `hora`, `ventasdeldia`.`Total` AS `Total`, `ventasdeldia`.`tipopago` AS `tipopago`, `ventasdeldia`.`caja` AS `caja`, `ventasdeldia`.`cierre` AS `cierre` FROM (select `ventas`.`id_v` AS `idtrans`,`usuarios`.`user_u` AS `usuario`,`ventas`.`hra_v` AS `hora`,`ventas`.`total_v` AS `Total`,(case when (`ventas`.`tipopago_v` = 'C') then 'Efectivo' when (`ventas`.`tipopago_v` = 'T') then 'Transferencia' else 'Otro' end) AS `tipopago`,`ventas`.`caja_v` AS `caja`,`caja`.`hrcierre_c` AS `cierre` from ((`ventas` join `usuarios`) join `caja`) where ((`ventas`.`emp_v` = `usuarios`.`id_u`) and (`ventas`.`caja_v` = `caja`.`id_c`))) AS `ventasdeldia` WHERE (`ventasdeldia`.`cierre` is null) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `ventasultcaja`
--
DROP TABLE IF EXISTS `ventasultcaja`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ventasultcaja`  AS SELECT `ventasdeldia`.`idtrans` AS `idtrans`, `ventasdeldia`.`usuario` AS `usuario`, `ventasdeldia`.`hora` AS `hora`, `ventasdeldia`.`Total` AS `Total`, `ventasdeldia`.`tipopago` AS `tipopago`, `ventasdeldia`.`caja` AS `caja` FROM (select `ventas`.`id_v` AS `idtrans`,`usuarios`.`user_u` AS `usuario`,`ventas`.`hra_v` AS `hora`,`ventas`.`total_v` AS `Total`,(case when (`ventas`.`tipopago_v` = 'C') then 'Efectivo' when (`ventas`.`tipopago_v` = 'T') then 'Transferencia' else 'Otro' end) AS `tipopago`,`ventas`.`caja_v` AS `caja` from ((`ventas` join `usuarios`) join `caja`) where ((`ventas`.`emp_v` = `usuarios`.`id_u`) and (`ventas`.`caja_v` = `caja`.`id_c`))) AS `ventasdeldia` WHERE (`ventasdeldia`.`caja` = (select max(`caja`.`id_c`) from `caja` limit 1)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_con_transacciones`
--
DROP TABLE IF EXISTS `vista_productos_con_transacciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_con_transacciones`  AS SELECT `u`.`id_p` AS `id_p`, `u`.`nom_p` AS `nom_p`, `u`.`cant_p` AS `cant_p`, `u`.`preciou_p` AS `preciou_p`, `u`.`estado_p` AS `estado_p`, `u`.`preventa_p` AS `preventa_p`, `u`.`img_p` AS `img_p`, ((select count(0) from `preventas` where (`preventas`.`prod_pr` = `u`.`id_p`)) + (select count(0) from `lista_venta` where (`lista_venta`.`prod_lv` = `u`.`id_p`))) AS `total_transacciones` FROM `productos` AS `u` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_usuarios_con_transacciones`
--
DROP TABLE IF EXISTS `vista_usuarios_con_transacciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_usuarios_con_transacciones`  AS SELECT `u`.`id_u` AS `id_u`, `u`.`user_u` AS `user_u`, `u`.`tipo_u` AS `tipo_u`, `u`.`tel_u` AS `tel_u`, `u`.`email_u` AS `email_u`, `u`.`estado_u` AS `estado_u`, (((select count(0) from `preventas` where (`preventas`.`emp_pr` = `u`.`id_u`)) + (select count(0) from `ventas` where (`ventas`.`emp_v` = `u`.`id_u`))) + (select count(0) from `caja` where (`caja`.`emp_c` = `u`.`id_u`))) AS `total_transacciones` FROM `usuarios` AS `u` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`id_c`),
  ADD KEY `emp_c` (`emp_c`);

--
-- Indices de la tabla `lista_venta`
--
ALTER TABLE `lista_venta`
  ADD PRIMARY KEY (`id_lv`),
  ADD KEY `prod_lv` (`prod_lv`),
  ADD KEY `venta_lv` (`venta_lv`);

--
-- Indices de la tabla `preventas`
--
ALTER TABLE `preventas`
  ADD PRIMARY KEY (`id_pr`),
  ADD KEY `emp_pr` (`emp_pr`),
  ADD KEY `caja_pr` (`caja_pr`),
  ADD KEY `prod_pr` (`prod_pr`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_p`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_u`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_v`),
  ADD KEY `emp_v` (`emp_v`),
  ADD KEY `caja_v` (`caja_v`),
  ADD KEY `prev_v` (`prev_v`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id_c` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `lista_venta`
--
ALTER TABLE `lista_venta`
  MODIFY `id_lv` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT de la tabla `preventas`
--
ALTER TABLE `preventas`
  MODIFY `id_pr` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_p` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_u` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_v` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja`
--
ALTER TABLE `caja`
  ADD CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`emp_c`) REFERENCES `usuarios` (`id_u`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_venta`
--
ALTER TABLE `lista_venta`
  ADD CONSTRAINT `lista_venta_ibfk_1` FOREIGN KEY (`prod_lv`) REFERENCES `productos` (`id_p`) ON UPDATE CASCADE,
  ADD CONSTRAINT `lista_venta_ibfk_2` FOREIGN KEY (`venta_lv`) REFERENCES `ventas` (`id_v`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `preventas`
--
ALTER TABLE `preventas`
  ADD CONSTRAINT `preventas_ibfk_1` FOREIGN KEY (`emp_pr`) REFERENCES `usuarios` (`id_u`) ON UPDATE CASCADE,
  ADD CONSTRAINT `preventas_ibfk_2` FOREIGN KEY (`caja_pr`) REFERENCES `caja` (`id_c`) ON UPDATE CASCADE,
  ADD CONSTRAINT `preventas_ibfk_3` FOREIGN KEY (`prod_pr`) REFERENCES `productos` (`id_p`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`emp_v`) REFERENCES `usuarios` (`id_u`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`caja_v`) REFERENCES `caja` (`id_c`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`prev_v`) REFERENCES `preventas` (`id_pr`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
