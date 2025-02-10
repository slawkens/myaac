const fse = require('fs-extra');
const path = require('path');

const nodeModulesDir = path.join(__dirname, 'node_modules');
const publicDir = path.join(__dirname, 'tools/ext');

fse.emptyDirSync(path.join(publicDir, 'jquery'));
fse.emptyDirSync(path.join(publicDir, 'jquery-ui'));
fse.emptyDirSync(path.join(publicDir, 'bootstrap'));
fse.emptyDirSync(path.join(publicDir, 'tinymce'));
fse.emptyDirSync(path.join(publicDir, 'tinymce-jquery'));
fse.copySync(path.join(nodeModulesDir, 'jquery', 'dist'), path.join(publicDir, 'jquery'), { overwrite: true });
fse.copySync(path.join(nodeModulesDir, 'jquery-ui', 'dist'), path.join(publicDir, 'jquery-ui'), { overwrite: true });
fse.copySync(path.join(nodeModulesDir, 'bootstrap', 'dist'), path.join(publicDir, 'bootstrap'), { overwrite: true });
fse.copySync(path.join(nodeModulesDir, 'tinymce'), path.join(publicDir, 'tinymce'), { overwrite: true });
fse.copySync(path.join(nodeModulesDir, '@tinymce', 'tinymce-jquery', 'dist'), path.join(publicDir, 'tinymce-jquery'), { overwrite: true });
