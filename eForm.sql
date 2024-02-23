/*Database per Sondaggi Online       */

DROP DATABASE IF EXISTS eForm;
CREATE DATABASE eForm;
USE eForm;

/*Tabella per Utente      */
CREATE TABLE Utente (
	Email VARCHAR(128) PRIMARY KEY,
    Nome VARCHAR(32),
    Cognome VARCHAR(64),
    Luogo_Nascita VARCHAR(32), 
    Anno_Nascita INT(4),
    Totale_Bonus FLOAT
)	ENGINE=INNODB;

/*Tabella per l'Utente Premium   */
CREATE TABLE Premium (
	Utente VARCHAR(128) PRIMARY KEY,
    Data_Inizio_Abb DATE,
    Data_Fine_Abb DATE,
    Costo FLOAT,
    Num_Sondaggi INT,
		FOREIGN KEY (Utente) REFERENCES Utente(Email) ON DELETE CASCADE
)	ENGINE=INNODB;
    
/*Tabella per l'Utente Administrator  */
CREATE TABLE Administrator (
	Utente VARCHAR(128) PRIMARY KEY,
		FOREIGN KEY (Utente) REFERENCES Utente(Email) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per Azienda      */
CREATE TABLE Azienda (
	Codice_Fiscale BIGINT(11) PRIMARY KEY,
    Email VARCHAR(128),
    Nome VARCHAR(64),
    Sede VARCHAR(64)
)	ENGINE=INNODB;

/*Tabella per Domanda                    */
CREATE TABLE Domanda (
	Id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    Testo VARCHAR(255),
    Foto MEDIUMBLOB,
    Punteggio INT, 
    Premium VARCHAR(128), 
    Azienda BIGINT(11), /*Per società o enti il codice fiscale è un numero di 11 cifre*/
		FOREIGN KEY (Premium) REFERENCES Premium(Utente) ON DELETE CASCADE,
		FOREIGN KEY (Azienda) REFERENCES Azienda(Codice_Fiscale) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per Domanda Chiusa         */
CREATE TABLE Domanda_Chiusa (
	Domanda SMALLINT NOT NULL,
		FOREIGN KEY (Domanda) REFERENCES Domanda(Id) ON DELETE CASCADE
) ENGINE = INNODB;

/*Tabella per Domanda Aperta         */
CREATE TABLE Domanda_Aperta (
	Domanda SMALLINT NOT NULL, 
    Max_Caratteri INT,
		FOREIGN KEY (Domanda) REFERENCES Domanda(Id) ON DELETE CASCADE
) ENGINE = INNODB;

/*Tabella per Opzione     */
CREATE TABLE Opzione (
	Domanda SMALLINT NOT NULL,
    Numero_Progressivo SMALLINT NOT NULL,
    Testo VARCHAR(64),
		FOREIGN KEY (Domanda) REFERENCES Domanda_Chiusa(Domanda) ON DELETE CASCADE      
)	ENGINE=INNODB;

/*Tabella per Premio        */
CREATE TABLE Premio (
	Nome VARCHAR(32) PRIMARY KEY, 
    Descrizione VARCHAR(255),
    Min_Punti INT,
    Foto MEDIUMBLOB, 
    Administrator VARCHAR(128) NOT NULL,
		FOREIGN KEY (Administrator) REFERENCES Administrator(Utente) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per il Dominio     */
CREATE TABLE Dominio (
	Parola_Chiave VARCHAR(32) PRIMARY KEY,
    Descrizione VARCHAR(255)
)	ENGINE=INNODB;

/*Tabella per Sondaggio        */
CREATE TABLE Sondaggio (
	Codice SMALLINT PRIMARY KEY AUTO_INCREMENT,
    Titolo VARCHAR(64),
    Stato ENUM('Aperto', 'Chiuso'),
    Data_Creazione DATE,
    Data_Chiusura DATE,
    Max_Utenti INT,
    Dominio VARCHAR(32) NOT NULL,
    Premium VARCHAR(128),
    Azienda BIGINT(11),
		FOREIGN KEY (Dominio) REFERENCES Dominio(Parola_Chiave) ON DELETE CASCADE,
        FOREIGN KEY(Premium) REFERENCES Premium(Utente) ON DELETE CASCADE,
        FOREIGN KEY (Azienda) REFERENCES Azienda(Codice_Fiscale) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per l'Invito    */
CREATE TABLE Invito (
	Cod INT PRIMARY KEY auto_increment, 
    Esito ENUM('Accettato', 'Rifiutato'),
    Sondaggio SMALLINT NOT NULL,
    Utente VARCHAR(128) NOT NULL,
    Premium VARCHAR(128),
    Azienda BIGINT(11),
		FOREIGN KEY (Sondaggio) REFERENCES Sondaggio(Codice) ON DELETE CASCADE,
		FOREIGN KEY (Utente) REFERENCES Utente(Email) ON DELETE CASCADE,
        FOREIGN KEY (Premium) REFERENCES Premium(Utente) ON DELETE CASCADE, 
		FOREIGN KEY (Azienda) REFERENCES Azienda(Codice_Fiscale) ON DELETE CASCADE
)	ENGINE=INNODB;
    
/*Tabella per Composto    */
CREATE TABLE Composto (
	Sondaggio SMALLINT NOT NULL,
    Domanda SMALLINT NOT NULL,
		FOREIGN KEY (Sondaggio) REFERENCES Sondaggio(Codice) ON DELETE CASCADE,
        FOREIGN KEY (Domanda) REFERENCES Domanda(Id) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per Storico      */
CREATE TABLE Storico (
	Premio VARCHAR(32) NOT NULL,
    Utente VARCHAR(128) NOT NULL,
		FOREIGN KEY (Premio) REFERENCES Premio(Nome) ON DELETE CASCADE,
        FOREIGN KEY (Utente) REFERENCES Utente(Email) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per Risposte      */
CREATE TABLE Risposte (
	Utente VARCHAR(128) NOT NULL,
    Domanda SMALLINT,
    Risposta TEXT,
		FOREIGN KEY (Utente) REFERENCES Utente(Email) ON DELETE CASCADE,
		FOREIGN KEY (Domanda) REFERENCES Domanda(Id) ON DELETE CASCADE
)	ENGINE=INNODB;

/*Tabella per Interesse              */
CREATE TABLE Interesse (
	Dominio VARCHAR(32) NOT NULL,
    Utente VARCHAR(128) NOT NULL,
		FOREIGN KEY (Dominio) REFERENCES Dominio(Parola_Chiave) ON DELETE CASCADE,
        FOREIGN KEY (Utente) REFERENCES Utente(Email) ON DELETE CASCADE
)	ENGINE=INNODB;      


/*
*
*
*
*
*
*
*
*
*
*
*
*
*
*Definizione delle Stored Procedure                            */

/*Inserimento di un nuovo Utente        
*                       
* il campo 'Totale_Bonus' viene di default settato a 0
*/
DELIMITER $
CREATE PROCEDURE InsertUtente(IN mail VARCHAR(128), Nome VARCHAR(32), Cognome VARCHAR(64), Luogo_Nascita VARCHAR(32), Anno_Nascita INT(4), OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Utente WHERE (mail = Email));
    IF (i = 0) THEN 
		INSERT INTO Utente values (mail, Nome, Cognome, Luogo_Nascita, Anno_Nascita, 0);
		SET Ok = TRUE;
    ELSE SET Ok = FALSE;
    END IF;
	END $
DELIMITER ;

/*Inserimento di un Utente Amministratore nella tabella Administrator      */
DELIMITER $
CREATE PROCEDURE InsertAdmin(IN mail VARCHAR(128))
BEGIN
	DECLARE i INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Utente WHERE (mail = Email));
    IF (i = 1) THEN INSERT INTO Administrator values (mail);
    END IF;
END $
DELIMITER ;

/*Controlla se le date d'inizio e fine abbonamento inserite per la registrazione di un utente Premium sono valide    */
DELIMITER $
CREATE PROCEDURE erCheckData(inizio DATE, fine DATE, OUT Ok BOOLEAN)
BEGIN
	DECLARE oggi DATE;
	SET oggi = curdate();
    IF(inizio <= fine AND inizio>= oggi) THEN
		SET Ok = TRUE;
	ELSE 
		SET Ok = FALSE;
	END IF;
END $
DELIMITER ;

/*Inserimento di un Utente Premium nella tabella Premium    
*    
* il campo 'Num_Sondaggi' viene inizialmente settato a 0
*/
DELIMITER $
CREATE PROCEDURE InsertPremium(IN mail VARCHAR(128), Data_Inizio_Abb DATE, Data_Fine_Abb DATE, Costo FLOAT)
BEGIN
	DECLARE i INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Utente WHERE (mail = Email));
    IF (i = 1) THEN INSERT INTO Premium values (mail, Data_Inizio_Abb, Data_Fine_Abb, Costo, 0);
    END IF;
END $
DELIMITER ;

/*Inserimento di una nuova Azienda              */
DELIMITER $
CREATE PROCEDURE InsertAzienda(IN cf BIGINT(11), Email VARCHAR(128), Nome VARCHAR(64), Sede VARCHAR(64), OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Azienda WHERE (cf = Codice_Fiscale));
    IF (i = 0) THEN 
		INSERT INTO Azienda values (cf, Email, Nome, Sede);
		SET Ok = TRUE;
    ELSE SET Ok = FALSE;
    END IF;
	END $
DELIMITER ;
    
/*Inserimento di un nuovo Premio            */
DELIMITER $
CREATE PROCEDURE InsertPremio(IN NomePremio VARCHAR(32), Descrizione VARCHAR(255), Min_Punti INT, Foto MEDIUMBLOB, Admi  VARCHAR(128), OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
	SET i = (SELECT COUNT(*) FROM Premio WHERE (NomePremio = Nome));
    if (i = 0) THEN
		INSERT INTO Premio values(NomePremio, Descrizione, Min_Punti, Foto, Admi);
		SET Ok = TRUE;
    ELSE SET Ok = FALSE;
    end if;
END $
DELIMITER ;

/*Inserimento di un nuovo Dominio                */
DELIMITER $
CREATE PROCEDURE InsertDominio(IN key_word VARCHAR(32), Descrizione VARCHAR(255), OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
	SET i = (SELECT COUNT(*) FROM Dominio WHERE (key_word = Parola_Chiave));
    IF (i = 0) THEN
		INSERT INTO Dominio VALUES(key_word, Descrizione);
        SET Ok = TRUE;
	ELSE
		SET Ok = FALSE;
    END IF;
END $
DELIMITER ;

/*Inserimento di una nuova Domanda                       */
/*Successivo inserimento nella tabella di appartenza Aperta( Tipo = 1) o Chiusa( Tipo = 2) */
DELIMITER $
CREATE PROCEDURE InsertDomanda(Testo VARCHAR(255), Foto MEDIUMBLOB, Punteggio INT, Max_Caratteri INT, Premium VARCHAR(128), Azienda BIGINT(11), Tipo INT)
BEGIN
	DECLARE idDomanda SMALLINT DEFAULT 0; 
	INSERT INTO Domanda(Testo, Foto, Punteggio, Premium, Azienda) VALUES(Testo, Foto, Punteggio, Premium, Azienda);
    SET idDomanda = (SELECT Id FROM Domanda ORDER BY Id DESC LIMIT 1);
    IF(Tipo = 1) THEN
		INSERT INTO Domanda_Aperta VALUES (idDomanda, Max_Caratteri);
	ELSEIF (Tipo = 2) THEN
		INSERT INTO Domanda_Chiusa VALUES (idDomanda);
	END IF;
END $
DELIMITER ;

/*Inserimento di un nuovo Sondaggio           */
DELIMITER $
CREATE PROCEDURE InsertSondaggio(Tit VARCHAR(64), Data_I DATE, Data_C DATE, Max_U INT, Dom VARCHAR(32), Prem VARCHAR(128), Az BIGINT(11), OUT Ok BOOLEAN)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE today DATE;
    SET today = (SELECT CURDATE());
    SET i = (SELECT COUNT(*) FROM Dominio WHERE (Dom = Parola_Chiave));
    IF (i = 1) THEN                                    /* La data di creazione dev'essere inferiore( o uguale) a quella di chiusura,*/
		IF(Data_I <= Data_C and Data_I >= today) THEN  /* e maggiore( o uguale) a quella odierna.                                   */
			INSERT INTO Sondaggio(Titolo, Stato, Data_Creazione, Data_Chiusura, Max_Utenti, Dominio, Premium, Azienda) values (Tit, 1, Data_I, Data_C, Max_U, Dom, Prem, Az);
            SET Ok = TRUE;
		ELSE SET Ok = FALSE;
		END IF;
    END IF;
END $
DELIMITER ;

/*Invito Utente ad un Sondaggio da parte di un utente Premium                        */
DELIMITER $
CREATE PROCEDURE InvitaUtente(Mittente VARCHAR(128), Destinatario VARCHAR(128), Sond SMALLINT, OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
	DECLARE n INT DEFAULT 0;
    DECLARE m INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Sondaggio WHERE (Sond = Codice) AND (Stato = 'Aperto')); 
    SET n = (SELECT COUNT(*) FROM Utente WHERE (Destinatario = Email));
    /*se un utente è già stato invitato ad un sondaggio non può venire invitato nuovamente al medesimo sondaggio */
    SET m = (SELECT COUNT(*) FROM Invito WHERE(Sond = Sondaggio AND Destinatario = Utente)); 
    IF (i = 1 AND n = 1 AND m = 0) THEN
		/* l'esito del sondaggio è settato di default vuoto(0) "", e l'azienda è null       */ 
		INSERT INTO Invito(Esito, Sondaggio, Utente, Premium, Azienda) values (0, Sond, Destinatario, Mittente, null);
        SET Ok = TRUE;
	ELSE 
		SET Ok = FALSE;
	END IF;	
END $
DELIMITER ;

/*Invita un Utente ad un Sondaggio da parte di un'Azienda          */
DELIMITER $
CREATE PROCEDURE InvitaUtentiAz(aziend BIGINT, sond SMALLINT, utent VARCHAR(128), OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
	DECLARE l INT DEFAULT 0;
    DECLARE m INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Azienda WHERE aziend = Codice_Fiscale);
    SET l = (SELECT COUNT(*) FROM Sondaggio WHERE sond = Codice);
    SET m = (SELECT COUNT(*) FROM Utente WHERE utent = Email);
    IF(i = 1 AND l = 1 AND m = 1) THEN
		INSERT INTO Invito(Esito, Sondaggio, Utente, Premium, Azienda) VALUES (0, sond, utent, null, aziend);
        SET Ok = TRUE;
	ELSE
		SET Ok = FALSE;
    END IF;
END $
DELIMITER ;

/*Collegamento di un Utente ad un Dominio d'interesse                  */ 

DELIMITER $
CREATE PROCEDURE CollegaDominio(mail VARCHAR(128), dom VARCHAR(32), OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
    DECLARE n INT DEFAULT 0;
    DECLARE m INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Utente WHERE (mail = Email));
    SET n = (SELECT COUNT(*) FROM Dominio WHERE (dom = Parola_Chiave));
    IF (i = 1 AND n = 1) THEN
		SET m = (SELECT COUNT(*) FROM Interesse WHERE mail = Utente AND dom = Dominio);
        IF (m = 0) THEN
			INSERT INTO Interesse values (dom, mail);
			SET Ok = TRUE;
		ELSE 
			SET Ok = FALSE;
		END IF;
	ELSE
		SET Ok = FALSE;
	END IF;
END $
DELIMITER ;

/*Rispondi ad un invito           */
DELIMITER $
CREATE PROCEDURE RispondiInvito(Codice INT, Risposta INT, OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
    DECLARE e INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Invito WHERE Codice = Cod);
    IF (i = 1) THEN
		SET e = (SELECT Esito FROM Invito WHERE Codice = Cod);
        IF (e = 0) THEN
			UPDATE Invito SET Esito = Risposta WHERE Cod = Codice;
			SET Ok = TRUE;
		ELSE
			SET Ok = FALSE;
		END IF;
	END IF;
END $
DELIMITER ;

/*Collega una Domanda ad un Sondaggio                              */
DELIMITER $
CREATE PROCEDURE CollegaSondaggio(cod SMALLINT, id_code SMALLINT, OUT Ok BOOLEAN)
BEGIN                            
	DECLARE i INT DEFAULT 0;
    DECLARE n INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Sondaggio WHERE (cod = Codice));     
	SET n = (SELECT COUNT(*) FROM Domanda WHERE (id_code = Id));
	IF (i = 1 AND n = 1) THEN
		INSERT INTO Composto values (cod, id_code);
		SET Ok = TRUE;
	ELSE 
		SET Ok = FALSE;
	END IF;
END $
DELIMITER ;

/*Inserisci una Risposta ad una Domanda di un Sondaggio                    */
DELIMITER $
CREATE PROCEDURE InserisciRisposta(IN mail VARCHAR(128), codice SMALLINT, answer TEXT, OUT Ok BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
	DECLARE l INT DEFAULT 0;
    DECLARE m INT DEFAULT 0;
    SET i = (SELECT COUNT(*) FROM Utente WHERE mail = Email);
    SET l = (SELECT COUNT(*) FROM Domanda WHERE codice = Id);
    SET m = (SELECT COUNT(*) FROM Risposte WHERE (mail = Utente AND codice = Domanda));
    IF(i = 1 AND l = 1 AND m = 0) THEN
		INSERT INTO Risposte VALUES (mail, codice, answer); 
        SET Ok = TRUE;
	ELSE 
		SET Ok = FALSE;
	END IF;
END $
DELIMITER ;

/*controlla se un Utente ha già risposto ad una Domanda                  */
DELIMITER $
CREATE PROCEDURE ContaRisposte(questionId SMALLINT, mail VARCHAR(128), OUT notAnsYet BOOLEAN, OUT Ok BOOLEAN)
BEGIN
	DECLARE n INT DEFAULT 0;
    DECLARE m INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    SET n = (SELECT COUNT(*)FROM Utente WHERE mail = Email);
	SET m = (SELECT COUNT(*)FROM Domanda WHERE questionId = Id);
	IF(n = 1 AND m = 1) THEN
		SET Ok = TRUE;
		SET i = (SELECT COUNT(*) FROM Risposte WHERE questionId = mail AND mail = Utente);
        IF(i = 0) THEN
			SET notAnsYet = TRUE;
		ELSE 
			SET notAnsYet = FALSE;
		END IF;
	ELSE
		SET Ok = FALSE;
	END IF;
END $
DELIMITER ;

/*Inserisci Opzione riferita ad una Domanda_Chiusa                  */
DELIMITER $
CREATE PROCEDURE InserisciOpzione(domId SMALLINT, testo VARCHAR(64), OUT OptionOk BOOLEAN)
BEGIN
	DECLARE i INT DEFAULT 0;
    DECLARE l INT DEFAULT 0;
	DECLARE nuovoNumeroProgressivo SMALLINT DEFAULT 1;
    SET i = (SELECT COUNT(*) FROM Domanda_Chiusa WHERE domId = Domanda);
    SET l = (SELECT COUNT(*) FROM Opzione WHERE domId = Domanda);
    IF(l != 0) THEN
		SET nuovoNumeroProgressivo = l + 1;
	END IF;
    IF(i = 1) THEN
		INSERT INTO Opzione VALUES(domId, nuovoNumeroProgressivo, testo);
        SET OptionOk = TRUE;
	ELSE
		SET OptionOk = FALSE;
	END IF;
END $
DELIMITER ;

/*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
* Definizione dei trigger                       */

/*Trigger per incrementare di 1 il campo Premium.Num_Sondaggi quando un utente premium inserisce un nuovo sondaggio */

DELIMITER |
CREATE TRIGGER incrementaNumSondaggi AFTER INSERT ON Sondaggio FOR EACH ROW
BEGIN
    DECLARE premiumUser VARCHAR(128);
    SET premiumUser = NEW.Premium;
    IF( premiumUser IS NOT NULL ) THEN
        UPDATE Premium SET Num_Sondaggi = (Num_Sondaggi + 1) WHERE Utente = premiumUser;
    END IF;
END |
DELIMITER ;

/* Trigger per incrementare di 0.5 il punteggio di un Utente quando risponde ad un Invito ad un Sondaggio
*  Nel caso il suo punteggio raggiunga la soglia di un premio, quest'ultimo gli viene assegnato.     */

DELIMITER |
CREATE TRIGGER incrementaPunteggio_assegnaPremio AFTER UPDATE ON Invito FOR EACH ROW
BEGIN 
	DECLARE risposta INT;
    DECLARE punteggio FLOAT;
    DECLARE utent VARCHAR(128);
    DECLARE premio VARCHAR(32);
    SET premio = NULL;
    SET risposta = NEW.Esito;
    /* Esito è un campo ENUM: [1]-> 'Accettato', [2]-> 'Rifiutato' */
    IF (risposta = 1) THEN
		UPDATE Utente SET Totale_Bonus = (Totale_Bonus + 0.5) WHERE Email = NEW.Utente;
        SET punteggio = (SELECT Totale_Bonus FROM Utente WHERE Email = NEW.Utente);
        SET premio = (SELECT Nome FROM Premio WHERE Min_Punti = punteggio);
        SET utent = (SELECT Email FROM Utente WHERE Email = NEW.Utente);
        IF (premio IS NOT NULL) THEN
			INSERT INTO Storico VALUES (premio, utent);
        END IF;
	END IF;
END |
DELIMITER ;

/*Evento per tener traccia delle date dei sondaggi e, nel caso la data odierna superi la Data_Chiusura, aggiornarne lo Stato -> 'Chiuso'*/

SET GLOBAL event_scheduler = ON;
DELIMITER &
CREATE EVENT dateChange ON SCHEDULE EVERY 1 DAY DO 
BEGIN
	DECLARE oggi DATE;
    DECLARE n SMALLINT DEFAULT 0;
	DECLARE i SMALLINT DEFAULT 0;
	DECLARE closingDate DATE;
    SET oggi = curdate();
    SET n = (SELECT Codice FROM Sondaggio ORDER BY Codice DESC LIMIT 1); 
	SET i = 1;
    SET closingDate = NULL;
    WHILE i <= n DO 
		SET closingDate = (SELECT Data_Chiusura FROM Sondaggio WHERE i = Codice);
        IF(closingDate IS NOT NULL) THEN
			IF(closingDate < oggi) THEN
				UPDATE Sondaggio SET Stato = 2 WHERE i = Codice;
            END IF;
        END IF;
        SET i = i + 1;
        SET closingDate = NULL;
    END WHILE;
END & 
DELIMITER ;

/*Trigger per il cambio di Stato(->'Chiuso') di un Sondaggio se il numero di inviti accettati raggiunge il Max_Utenti */

DELIMITER |
CREATE TRIGGER chiudiSondaggio AFTER UPDATE ON Invito FOR EACH ROW
BEGIN
	DECLARE maxUtenti INT;
    DECLARE invitiAccettati INT DEFAULT 0;
    SET maxUtenti = (SELECT Max_Utenti FROM Sondaggio WHERE Codice = NEW.Sondaggio);
    SET invitiAccettati = (SELECT COUNT(*) FROM Invito WHERE Esito = 1 AND Sondaggio = NEW.Sondaggio);
    IF (invitiAccettati = maxUtenti) THEN
		UPDATE Sondaggio SET Stato = 2 WHERE Codice = NEW.Sondaggio;
    END IF;
END |
DELIMITER ;
