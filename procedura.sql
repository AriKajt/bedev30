-- kreiranje baze podatka i tablice

DROP TABLE `trgovina` IF EXISTS;
CREATE DATABASE IF NOT EXISTS `trgovina` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `trgovina`;

DROP TABLE `proizvodi` IF EXISTS;
CREATE TABLE IF NOT EXISTS `proizvodi` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naziv` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    `cijena` DECIMAL(10, 2) NOT NULL,
    `kolicina` INT UNSIGNED NOT NULL
);


-- procedura za ažuriranje zaliha

DELIMITER $$

CREATE PROCEDURE azuriraj_zalihe (IN p_id_proizvoda INT, IN p_kolicina_prodana INT)
BEGIN
    DECLARE v_trenutna_kolicina INT;

    -- početak transakcije
    START TRANSACTION;
    
    -- dohvati trenutnu količinu proizvoda na skladištu
    SELECT kolicina INTO v_trenutna_kolicina
        FROM zalihe
        WHERE id_proizvoda = p_id_proizvoda;
        FOR UPDATE; -- eliminiramo race condition, zaključavamo dok ne obavimo update
    
    -- provjeri ako je proizvod na skladištu
    IF v_trenutna_kolicina IS NULL THEN
        -- ako proizvod ne postoji, poništi transakciju
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Proizvod ne postoji na skladištu';
    ELSE
        -- provjeri da količina na skladištu neće biti negativna
        IF v_trenutna_kolicina - p_kolicina_prodana < 0 THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Nema dovoljno zaliha za ovu transakciju';
        ELSE
            -- ažuriraj stanje zaliha
            UPDATE zalihe
                SET kolicina = kolicina - p_kolicina_prodana
                WHERE id_proizvoda = p_id_proizvoda;
            
            -- Zatvori transakciju
            COMMIT;
        END IF;
    END IF;
END$$

DELIMITER ;

 -- pozivanje pohranjene procedure za ažuriranje zaliha:

   CALL AžurirajZalihe(869, 15);  -- smanji količinu proizvoda s ID 869 za 15 komada



-- funkcija za dohvat trenutne količine

DELIMITER $$

CREATE FUNCTION trenutna_kolicina (p_id_proizvoda INT) 
RETURNS INT

BEGIN
    DECLARE v_kolicina INT;
    
    -- dohvati trenutnu količinu proizvoda na skladištu
    SELECT kolicina INTO v_kolicina
    FROM zalihe
    WHERE id_proizvoda = p_id_proizvoda;
    
    -- ako proizvod ne postoji, vrati NULL
    IF v_kolicina IS NULL THEN
        RETURN NULL;
    END IF;
    
    -- inače vrati trenutnu količinu
    RETURN v_kolicina;
END$$

DELIMITER ;

-- pozivanje funkcije za provjeru trenutne količine:

   SELECT TrenutnaKolicina(699);  -- vraća trenutnu količinu proizvoda s ID 699

