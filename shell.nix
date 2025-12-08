let
    nixpkgs = fetchTarball {
        # Oct 31, 2025
        url = "https://github.com/NixOS/nixpkgs/archive/66a437ebcf6160152336e801a7ec289ba2aba3c5.tar.gz";
    };

    lockedPkgs = import nixpkgs {
        config = {
            allowUnfree = true;
        };
    };
in
{
    pkgs ? lockedPkgs,
    php-version ? 8.3,
    with-blackfire ? false,
    with-xdebug ? false,
    with-pcov ? !with-blackfire
}:

let
    base-php = if php-version == 8.3 then
        pkgs.php83
    else if php-version == 8.4 then
        pkgs.php84
    else
        throw "Unknown php version ${php-version}";

    php = pkgs.callPackage ./.nix/pkgs/php/package.nix {
        php = base-php;
        inherit with-pcov with-xdebug with-blackfire;
    };
in
pkgs.mkShell {
    buildInputs = [
        php
        php.packages.composer
        pkgs.starship
        pkgs.figlet
        pkgs.act
    ]
        ++ pkgs.lib.optional with-blackfire pkgs.blackfire
    ;

    shellHook = ''
    if [ -f "$PWD/.nix/shell/starship.toml" ]; then
        export STARSHIP_CONFIG="$PWD/.nix/shell/starship.toml"
    else
        export STARSHIP_CONFIG="$PWD/.nix/shell/starship.toml.dist"
    fi

    eval "$(${pkgs.starship}/bin/starship init bash)"

    clear
    figlet "PHP Matcher"
    '';
}