<?php
////////////////////////////////////////////////////////////////////////////////
//
// Copyright (C) 2001-2003 MARTINEAU Emeric (php4php@free.fr)
//
// Fichier gérant les fast tempate.
//
// Modifié le 4/03/2003 par Bubule
// - résolution du problème quand il y a dans la chaîne de caractères un =
//
// This program is free software; you can redistribute it and/or modify it under
// the terms of the GNU General Public License as published by the Free Software
// Foundation; either version 2 of the License, or (at your option) any later
// version.
//
// This program is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
// FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along with
// this program; if not, write to the Free Software Foundation, Inc., 59 Temple
// Place, Suite 330, Boston, MA 02111-1307 USA
//
////////////////////////////////////////////////////////////////////////////////
class fastTemplate
{
    // Répertoire de base du thème
    var $rootDir = "" ;

    // Tableau contenant les variables à convertir
    var $parseVar = array() ;

    // Tableau contenant les fonctions à executer
    var $parseFunction = array() ;

    // Tableau contenant les modules à afficher
    var $parseModule = array() ;

    // Temps en secondes que le script à mit pour interpréter le fichier
    var $parseTime = 0 ;

    // Indique si on est en mode strique et donc si on enregistre les erreurs
    var $strictMode = false ;

    // Contient les messages d'erreur
    var $errorMessage = array() ;

    // Variable contenant le résultat
    var $parseResult = "" ;

    ///////////////////////////////////////
    // Constructeur
    function fastTemplate($pathTemplate = "")
    {
        if (!ereg("^.+/$", $pathTemplate))
        {
            $pathTemplate .= "/" ;
        }

        $this->rootDir = $pathTemplate ;
    }

    ///////////////////////////////////////
    // Ajoute une variable à convertire
    function addVar($nomDeLaVariable, $valeurDeLaVariable = "")
    {
        if ($this->checkVarName("$nomDeLaVariable"))
        {
            if(gettype($nomDeLaVariable) == "array")
            {
                while ( list ($key,$val) = each ($nomDeLaVariable) )
                {
                    if (!(empty($key)))
                    {
                        // N'alloue pas les noms vides
                        $this->parseVar["$key"] = $val;
                    }
                }
            }
            else
            {
                // Les noms vides ne sont pas allouer
                if (!empty($nomDeLaVariable))
                {
                    $this->parseVar["$nomDeLaVariable"] = $valeurDeLaVariable;
                }
            }
        }
    }

    ///////////////////////////////////////
    // Supprime une variable à conversire
    function deleteVar($nomDeLaVariable)
    {
        unset($this->parseVar["$nomDeLaVariable"]) ;
    }

    ///////////////////////////////////////
    // Retourne la valeur de la variable
    function getVarValue($nomDeLaVariable)
    {
        if ((!isset($this->parseVar["$nomDeLaVariable"])) && $this->strictMode)
        {
            $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! La variable $nomDeLaVariable n'existe pas." ;
        }

        return $this->parseVar["$nomDeLaVariable"] ;
    }

    ///////////////////////////////////////
    // Ajoute un module à afficher
    function addModule($nomDuModule, $fichier = "")
    {
        if ($this->checkVarName("$nomDuModule"))
        {
            if(gettype($nomDuModule) == "array")
            {
                while ( list ($key,$val) = each ($nomDuModule) )
                {
                    if (!(empty($key)))
                    {
                        // N'alloue pas les noms vides
                        $this->parseModule["$key"] = $val;
                    }
                }
            }
            else
            {
                // Les noms vides ne sont pas allouer
                if (!empty($nomDuModule))
                {
                    $this->parseModule["$nomDuModule"] = $fichier;
                }
            }
        }
    }

    ///////////////////////////////////////
    // Supprime un module à afficher
    function deleteModule($nomDuModule)
    {
        unset($this->parseModule["$nomDuModule"]) ;
    }

    ///////////////////////////////////////
    // Retourne la valeur du module
    function getModuleValue($nomDuModule)
    {
        if ((!isset($this->parseModule["$nomDuModule"])) && $this->strictMode)
        {
            $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! Le module $nomDuModule n'existe pas." ;
        }

        return $this->parseModule["$nomDuModule"] ;
    }

    ///////////////////////////////////////
    // Ajoute une fonction à exécuter
    function addFunction($nomDeLaFonction, $code = "")
    {
        if ($this->checkVarName("$nomDeLaFonction"))
        {
            if(gettype($nomDeLaFonction) == "array")
            {
                while ( list ($key,$val) = each ($nomDeLaFonction) )
                {
                    if (!(empty($key)))
                    {
                        // N'alloue pas les noms vides
                        $this->parseFunction["$key"] = $val;
                    }
                }
            }
            else
            {
                // Les noms vides ne sont pas allouer
                if (!empty($nomDeLaFonction))
                {
                    $this->parseFunction["$nomDeLaFonction"] = $code;
                }
            }
        }
    }

    ///////////////////////////////////////
    // Supprime une fonction à exécuter
    function deleteFunction($nomDeLaFonction)
    {
        unset($this->parseFunction["$nomDeLaFonction"]) ;
    }

    ///////////////////////////////////////
    // Retourne la valeur de la fonction
    function getFunctionValue($nomDeLaFonction)
    {
        if ((!isset($this->parseFunction["$nomDeLaFonction"])) && $this->strictMode)
        {
            $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! La fonction $nomDeLaFonction n'existe pas." ;
        }

        return $this->parseFunction["$nomDeLaFonction"] ;
    }

    ///////////////////////////////////////
    // Passe en mode stricte
    function setStrictMode()
    {
        $this->strictMode = true ;
    }

    ///////////////////////////////////////
    // Passe en mode non stricte
    function unsetStrictMode()
    {
        $this->strictMode = false ;
    }

    ///////////////////////////////////////
    // Retourne la chaine contenant les erreurs
    function getErrorMessage()
    {
        return $this->errorMessage ;
    }

    ///////////////////////////////////////
    // Retourne le temps en seconde que le script a mis pour interpreter le fichier
    function getParseTime()
    {
        return $this->parseTime ;
    }

    ///////////////////////////////////////
    // Vérifie que le nom de la variable est valide
    function checkVarName($nomDeLaVariable)
    {
        return ereg("^[a-zA-Z][a-zA-Z0-9_]+$", $nomDeLaVariable) ;
    }

    ///////////////////////////////////////
    // Lit un fichier de variable
    function addVarInFile($fichier)
    {
        $fichier = $this->rootDir . $fichier ;

        if (file_exists($fichier))
        {
            if (is_readable($fichier))
            {
                // Met le fichier dans le tableau
                $contenuDuFichier = @file($fichier) ;

                for($j = 0; $j < @count($contenuDuFichier); $j++)
                {
                    // Supprime les espace, les cr/lf de début et fin
                    $contenuDuFichier[$j] = trim($contenuDuFichier[$j]) ;

                    if (!empty($contenuDuFichier[$j]))
                    {
                        // Sépare le nom de la variable de la donnéee
                        $resultat = @explode("=", $contenuDuFichier[$j]) ;

                        // A ETE DESACTIVE !
                        // On ne peut avoir que deux champs
                        //if (@count($resultat) == 2)
                        //{
                            // Récupère le nom de la variable
                            $nomDeLaVariable = trim($resultat[0]) ;

                            // Vérifie le nom de la variable
                            if ($this->checkVarName($nomDeLaVariable))
                            {
                                // Affecte la valeur de la variable à $resultat (avec les délimitateur)
                                for ($k = 2; $k < count($resultat); $k++)
                                {
                                    $resultat[1] .= "=" . $resultat[$k] ;
                                }

                                $resultat = trim($resultat[1]) ;

                                // Le premier caractère est forcement le délimitateur
                                $encadrementChaine = $resultat[0] ;

                                // Si le délimitateur est ' ou "
                                if (($encadrementChaine == "'") || ($encadrementChaine == '"'))
                                {
                                    $valeurDeLaVariable = "" ;

                                    // Récupère la valeur se place après le " ou '
                                    for ($i = 1; $i < (@strlen($resultat) - 1); $i++)
                                    {
                                        if ($resultat[$i] != $encadrementChaine)
                                        {
                                            // S'il y a un caractère d'omission
                                            if ($resultat[$i] == "\\")
                                            {
                                               // Saute au caractère suivant
                                               $i++ ;
                                               $valeurDeLaVariable = $valeurDeLaVariable . $resultat[$i] ;
                                            }
                                            else
                                            {
                                               // Copie le caractère
                                               $valeurDeLaVariable = $valeurDeLaVariable . $resultat[$i] ;
                                            }
                                        }
                                        else
                                        {
                                            // Fin d'encadrement donc on arrête
                                            break ;
                                        }
                                    }

                                    if ($this->strictMode)
                                    {
                                        if (($i + 1) != @strlen($resultat))
                                        {
                                            $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ligne " . ($j + 1) . " ! Des données ont été trouvées après la fin de chaine ou chaine non fermée." ;
                                        }
                                    }
                                }
                                else
                                {
                                    if ($this->strictMode)
                                    {
                                        $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ligne " . ($j + 1) . " ! Vous n'avez pas encarder la valeur de la variable $nomDeLaVariable avec ' ou \"." ;
                                    }
                                }
                            }

                            $this->addVar($nomDeLaVariable, $valeurDeLaVariable) ;
                        /*}
                        else
                        {
                            if ($this->strictMode)
                            {
                                $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur de syntaxe ligne " . ($j + 1) . " !" ;
                            }
                        }*/
                    }
                }
            }
            else
            {
                if ($this->strictMode)
                {
                      $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! Le fichier '$fichier' n'a pas les permissions pour être lu." ;
                }
            }
        }
        else
        {
            if ($this->strictMode)
            {
                $this->errorMessage[count($this->errorMessage)] = "Erreur ! Le fichier '$fichier' n'existe pas." ;
            }

        }
    }

    ///////////////////////////////////////
    // Interprete le fichier et le met dans parseResult
    function parse($nomDuFichier)
    {
        $nomDuFichier = $this->rootDir . $nomDuFichier ;

        // Calcule l'heure de début
        $debut = explode(" ", microtime()) ;
        $heureDeDebut = $debut[1] + $debut[0] ;

        if (file_exists($nomDuFichier))
        {
            if (is_readable($nomDuFichier))
            {
                // Met le fichier dans le tableau
                $contenuDuFichier = @file($nomDuFichier) ;

                // Pour chaque ligne
                for($ligne = 0; $ligne < @count($contenuDuFichier); $ligne++)
                {
                    $this->parseResult .= $this->parseLine($contenuDuFichier[$ligne]) ;
                }
            }
            else
            {
                if ($this->strictMode)
                {
                      $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! Le fichier '$nomDuFichier' n'a pas les permissions pour être lu." ;
                }
            }
        }
        else
        {
            if ($this->strictMode)
            {
                $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! Le fichier '$nomDuFichier' n'existe pas." ;
            }
        }

        // Calcule l'heure de fin
        $fin = explode(" ", microtime()) ;
        $heureDeFin = $fin[1] + $fin[0] ;

        // On a le temps de génération
        $this->parseTime = $heureDeFin - $heureDeDebut ;
    }

    ///////////////////////////////////////
    // Efface ce qui a été généré
    function clearResult()
    {
        $this->parseResult = "" ;
    }

    ///////////////////////////////////////
    // Fonction qui renvoit ce qui a été généré
    function getParseResult()
    {
        return $this->parseResult ;
    }

    ///////////////////////////////////////
    // Fonction qui affiche le résultat
    function printResult()
    {
        echo $this->parseResult ;
    }

    function parseLine($ligne)
    {
        $ligneParser = "" ;

        // Indique si on est dans un accolade ou non
        $in = false ;

        // Pour chaque caractère
        for($caractere = 0; $caractere < @strlen($ligne); $caractere++)
        {
            if ($in)
            {
                if ($ligne[$caractere] == '}')
                {
                    $nomDeLaVariableEnCours = trim($nomDeLaVariableEnCours) ;
                    $in = false ;

                    if (ereg("^function#", $nomDeLaVariableEnCours))
                    {
                        $fonction = substr($nomDeLaVariableEnCours, 9) ;

                        if (!empty($fonction))
                        {
                            // Par précausion on vide la variable
                            $STDOUT = "" ;

                            eval($this->getFunctionValue($fonction)) ;

                            $ligneParser .= $STDOUT ;
                        }
                    }
                    else if (ereg("^module#+", $nomDeLaVariableEnCours))
                    {
                        $module = substr($nomDeLaVariableEnCours, 7) ;

                        if (!empty($module))
                        {
                            if (file_exists($this->getModuleValue($module)))
                            {
                                // Par précausion on vide la variable
                                $STDOUT = "" ;
                                $STDERR = array() ;

                                // Evite les erreurs mais permet au fichier d'être ré-évalué
                                include_once($this->getModuleValue($module)) ;

                                $ligneParser .= $STDOUT ;
                            }
                            else
                            {
                                $this->errorMessage[count($this->errorMessage)] = "fasttemplate.php : Erreur ! Le fichier " . $this->getModuleValue($module) . " correspondant à $module est introuvable." ;
                            }
                        }
                    }
                    else
                    {
                        $ligneParser .= $this->getVarValue($nomDeLaVariableEnCours) ;
                    }
                }
                else
                {
                    // Copie le nom de la variable
                    $nomDeLaVariableEnCours .= $ligne[$caractere] ;
                }
            }
            else
            {
                if (($ligne[$caractere] == '\\') &&
                    ($ligne[$caractere + 1] == '{'))
                {
                    $ligneParser .= '{' ;
                }
                else if ($ligne[$caractere] == '{')
                {
                    $nomDeLaVariableEnCours = "" ;
                    $in = true ;
                }
                else
                {
                    $ligneParser .= $ligne[$caractere] ;
                }
            }
        }

        for($nbErreur = 0; $nbErreur < count($STDERR); $nbErreur++)
        {
            $this->errorMessage[count($this->errorMessage)] = $STDOUT[$STDERR] ;
        }

        return $ligneParser ;
    }
}
?>
