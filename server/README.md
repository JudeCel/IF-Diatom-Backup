#IF-Diatom-Backup
Tested on: Node version 4.2.0 LTS, NPM version 3.3.6

## Dependencies

NodeJs 4.2.0 or 4.2.x

NPM 3.3.6

MySQL 5.7

[ImageMagick](http://www.imagemagick.org/)

## Set up Project

```sh
cd project_path # path to project Example# /home/dainisl/code/IF-Diatom-Backup
cd server
cp config/config.json.sample config/config.json
npm install
```
> If runing command
```sh
npm start
```
>we get error about Diatom package then need installed this packages manually
```sh
npm install JudeCel/IF-Auth-Diatom --save
npm install JudeCel/IF-Common-Diatom --save
npm install JudeCel/IF-Data-Diatom --save
npm install JudeCel/IF-Reports-Diatom --save
npm install JudeCel/IF-TestHelpers-Diatom --save
```

### Import SQL dump
Database structure location $project_path/server/db/structure.sql
