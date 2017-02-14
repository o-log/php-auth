#!/bin/sh

export DEPLOY_RELEASE_DATE=`date -u +%Y%m%d%H%M%S`
export DEPLOY_PROD_CONFIG_FOLDER=Config_prod

echo \$RELEASE_DATE

git clone git@github.com:.../config.git ${DEPLOY_PROD_CONFIG_FOLDER}
mv ${DEPLOY_PROD_CONFIG_FOLDER}/Config.php Config/
rm -R Config_prod

#scp -r * ologinov@web.project.ru:/home/ologinov/ts/${RELEASE_DATE}
rsync -azvWPq --delete --exclude-from=RSYNC_EXCLUDES . ologinov@web.project.ru:/home/ologinov/ts/${RELEASE_DATE}
