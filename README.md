# SavedVariables Parser PHP
Parses SavedVariables file, to PHP array.

This script written for home use to parse World Of Warcraft auctions data file.

Do not use them in public servers, because somebody can remoley hack your site.

Primаry target of this script to be fast, in that reasion this not converts file to tokens, and parse tokens to multidim array.

Input exapmple:
```
AUCTIONATOR_SAVEDVARS = {
	["_5000000"] = 10000,
	["_50000"] = 500,
	["_200000"] = 1000,
	["_1000000"] = 2500,
	["_10000"] = 200,
	["_500"] = 5,
	["STARTING_DISCOUNT"] = 5,
	["_2000"] = 100,
}
```
Code
```
header('Content-Type: text/plain; charset=UTF-8');
print_r(SavedVariablesToArray('FILE_OF\Auctionator.lua'));
```
Results
```
Array
(
    [AUCTIONATOR_SAVEDVARS] => Array
        (
            [_5000000] => 10000
            [_50000] => 500
            [_200000] => 1000
            [_1000000] => 2500
            [_10000] => 200
            [_500] => 5
            [STARTING_DISCOUNT] => 5
            [_2000] => 100
        )
)
```



ps: If you wish to convert this, to parse real public files, you can use jailed environment, or just continue my work using my regular expressions.
pss: Free Software `MIT Licensed` free to use, for everyone.

With regards, LatDEV
