<?php
////////////////////////////////////////////////////////////////////////////////
//
// Copyright (C) 2004 MARTINEAU Emeric (php4php@free.fr)
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
header("Content-Type: text/html") ;
include("fasttemplate.php") ;

// Lecture du répertoire changelog
$fd = "" ;
$rep = "" ;
$listeRep = array() ;
$monTheme = new fastTemplate("themes/") ;
$monTheme->strictMode = false ;
$monTheme->addVar("URL", "http://php4php.free.fr/fastchangelog") ;

$basedir = "changelog/" ;

// verifie qu'on esaye pas de pirater
if (isset($_GET["ver"]))
{
    if (!(!(strpos($_GET["ver"], "..") === false) || eregi("[\|<>& \"\']+", $_GET["ver"]) ||
        ereg("^/.+", $_GET["ver"])))
    {
        $basedir .= $_GET["ver"] . "/" ;
    }
}

// Ouvre le répertoire
$fd = opendir($basedir) ;

while($rep = readdir($fd))
{
    if($rep != '..' && $rep !='.' && $rep !='')
    {
        if (is_dir($basedir . $rep))
        {
            $listeRep[count($listeRep)] = $rep ;
        }
    }
}

// Trie du tableau
rsort($listeRep) ;

$nb = count($listeRep) ;

$monTheme1 = new fastTemplate("themes/") ;
$monTheme1->strictMode = false ;


// Pour chaque version
for ($i = 0; $i < $nb; $i++)
{
    $monTheme->addVar("VERSION", $listeRep[$i]) ;

    if (isset($_GET["ver"]))
    {
        // Met le fichier en tableau
        $tab = @file($basedir . $listeRep[$i] . "/log.txt") ;

        @$monTheme->addVar("NAME", $tab[0]) ;
        @$monTheme->addVar("DATE", $tab[1]) ;

        $texte = "" ;

        $nb1 = count($tab) ;
        $monTheme1->parseResult = "" ;

        for ($j = 2; $j < $nb1 ; $j++)
        {
            if ($tab[$j][0] == "@")
            {
                $var = $tab[$j] ;

                // Supprime l'arobase @
                $var = substr($var, 1) ;

                // Récupère le numéro du bug
                sscanf($var, "%i", $bug) ;
                $monTheme1->addVar("BUG", $bug) ;

                // Récupère le complément d'information
                $text = substr($var, strlen($bug)) ;
                $monTheme1->addVar("COMPLEMENT", $text) ;

                @$monTheme1->parse("bugfix.htm") ;
            }
            else
            {
                $monTheme1->addVar("TEXTE", $tab[$j]) ;
                @$monTheme1->parse("ligne.htm") ;
            }
        }

        @$monTheme->addVar("CHANGE", $monTheme1->parseResult) ;

        @$monTheme->parse("titre-minor.htm") ;
    }
    else
    {
        @$monTheme->parse("titre-major.htm") ;
    }
}

$Resultat = $monTheme->parseResult ;
$monTheme->parseResult = "" ;

$monTheme->addVar("PAGE", $Resultat) ;

if (isset($_GET["ver"]))
{
    $monTheme1->parseResult = "" ;
    $monTheme1->addVar("URL", $monTheme->getVarValue("URL")) ;
    @$monTheme1->parse("retour.htm") ;
    $monTheme->addVar("RETOUR", $monTheme1->parseResult) ;
}

@$monTheme->parse("index.htm") ;
$monTheme->printResult() ;
?>
