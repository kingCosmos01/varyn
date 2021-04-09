/**
 * NOTE: This is a placeholder and it does not work! Someone needs to implement this script before it can be used.
 * Build the website. This module supports the following tasks:
 *   - optimize images (in place)
 *   - minify and combine ./public/common/*.js
 * Run this from the command line: npm run build -- --config ./bin/build-config.json --no-dryrun --no-verbose
 * @author: jf
 * @date: 14-Dec-2019
 */
const os = require("os");
const path = require("path");
const async = require("async");
const glob = require("glob");
const rename = require("gulp-rename");
const chalk = require("chalk");
const prettyBytes = require("pretty-bytes");
const ImageMin = require("imagemin");
const { minify } = require("terser");
const fsExtra = require("fs-extra");

// Local module variables
const numberOfCPUs = os.cpus().length;
let compressionStats = {
    totalFiles: 0,
    totalBytesConsidered: 0,
    totalBytesCompressed: 0,
    startTime: new Date(),
    endTime: null
};

// Configurable parameters:
let configuration = {
    dryrun: true,
    isLoggingInfo: true,
    isLoggingError: true,
    logfile: null,
    verbose: false,
    optimizeImages: false,
    packageName: "varyn.min.js",
    configurationFile: null,
    source: "./public/common",
    destination: "./distrib/common",
    exclude: null,
    imagesFileSpec: "{jpg,jpeg,png,gif}",
    unoptimizedFileSpec: "{eot,ttf,woff,woff2,svg,mp3,ogg,wav,json}",
    pageManifest: {
        varyn: [
            "varyn.js"
        ],
        allgames: [
            "varyn.js",
            "varynAllGamesPage.js"
        ],
        blog: [
            "varynBlog.js"
        ],
        homepage: [
            "varyn.js",
            "varynIndexPage.js"
        ],
        play: [
            "varyn.js",
            "varynPlayPage.js"
        ],
        profile: [
            "varyn.js",
            "varynProfilePage.js"
        ],
        resetPassword: [
            "varyn.js",
            "varynResetPasswordPage.js"
        ]
    },
    libManifest: [
        "modernizr.js",
        "jquery.min.js",
        "bootstrap.min.js",
        "ie10-viewport-bug-workaround.js",
        "enginesis.js",
        "ShareHelper.js",
        "commonUtilities.js"
    ],
    filesToCopy: [ // a list of files to copy to destination without modification
    ],
    jsFilesToIgnore: [ // a list of files in the js folder to skip
    ],
    libsToCopy: [
        "bootstrap.min.js",
        "head.min.js",
        "modernizr.js",
        "jquery.min.js"
    ],
    libsToCombine: [
        "commonUtilities.js",
        "ShareHelper.js",
        "enginesis.js"
    ],
    combinedLibFileName: "enginesis.min.js"
};


/**
 * Helper function to control logging.
 * @param {string} message
 */
function logInfo(message) {
    if (configuration.isLoggingInfo) {
        console.log(chalk.green(message));
    }
}
/**
 * Helper function to control logging.
 * @param {string} message
 */
function logError(message) {
    if (configuration.isLoggingError) {
        console.warn(chalk.red("ᚎ " + message));
    }
}

/**
 * Capture any command line arguments and update configuration variables.
 * @return {object} Args object.
 */
 function getCommandLineArguments() {
    const args = require("yargs")
    .options({
        "config": {
            alias: "c",
            type: "string",
            describe: "path to configuration file",
            demandOption: false,
            default: null
        },
        "destination": {
            alias: "d",
            type: "string",
            describe: "copy result /distrib folder to specified path",
            demandOption: false
        },
        "logfile": {
            alias: "l",
            type: "string",
            describe: "path to log file",
            demandOption: false
        },
        "source": {
            alias: "s",
            type: "string",
            describe: "set the source file root folder",
            demandOption: false
        },
        "optimizeImages": {
            alias: "i",
            type: "boolean",
            describe: "optimize image files",
            demandOption: false
        },
        "verbose": {
            alias: "v",
            type: "boolean",
            describe: "turn on extra debugging",
            demandOption: false
        },
        "exclude": {
            alias: "x",
            type: "string",
            describe: "path to exclude file list (text file)",
            demandOption: false
        },
        "dryrun": {
            alias: "y",
            type: "boolean",
            describe: "perform dry run (no actual copy)",
            demandOption: false
        },
    })
    .alias("?", "help")
    .help()
    .argv;
    return args;
}

/**
 * Load the configuration information from a JSON file.
 * @param {string} configurationFilePath path to a configuration file.
 * @returns {Promise} Resolves with the configuration data or an empty object if no data is available.
 */
function loadConfigurationData(configurationFilePath) {
    return new Promise(function(resolve) {
        fsExtra.readJSON(configurationFilePath)
        .then(function(configuration) {
            resolve(configuration);
        })
        .catch(function(exception) {
            logError(`Configuration file ${configurationFilePath} error when reading: ${exception.toString()}`);
            resolve({});
        });
    });
}

function matchProperties(configurationProperties) {
    const configurableProperties = ["destination", "dryrun", "exclude", "logfile", "optimizeImages", "source", "verbose"];
    configurableProperties.forEach(function(property) {
        if (configurationProperties.hasOwnProperty(property)) {
            logInfo(">>> Setting ." + property + " to " + configurationProperties[property]);
            configuration[property] = configurationProperties[property];
        };
    });
}

/**
 * Merge configuration options from command line, configuration file, with default configuration. Command line
 * overrides configuration file which overrides defaults.
 */
function updateConfiguration() {
    const cliArgs = getCommandLineArguments();

    return new Promise(function(resolve) {
        if (cliArgs.config != null) {
            fsExtra.pathExists(cliArgs.config)
            .then(function(configurationFileExists) {
                if (configurationFileExists) {
                    logInfo(`Configuration file ${cliArgs.config} overriding any matching default options.`);
                    loadConfigurationData(cliArgs.config)
                    .then(function(configurationData) {
                        // iterate over configuration and replace matching properties.
                        matchProperties(configurationData);
                        // override configuration with anything on CLI
                        logInfo(`CLI overriding any matching options.`);
                        matchProperties(cliArgs);
                        resolve();
                    });
                } else {
                    logError(`Configuration file ${cliArgs.config} does not exist, continuing with the default configuration.`);
                    // override configuration with anything on CLI
                    matchProperties(cliArgs);
                    resolve();
                }
            });
        }    
    });
}

/**
 * Optimize all image files found in sourcePath and copy the optimized version to destinationPath.
 * @param {string} sourcePath Path to the root folder under which to find images. Image files are jpg, jpeg, png, gif, svg.
 * @param {string} destinationPath Path where to copy optimized files.
 * @param {string} imagesGlobSpec Glob specification of files to match in source path.
 */
function optimizeImages(sourcePath, destinationPath, imagesGlobSpec) {
    logInfo("ᗘ Starting image optimization");
    var globSpec = path.join(sourcePath, "/**/") + "*." + imagesGlobSpec;
    var sourcePathLength = sourcePath.length;
    var totalBytesConsidered = 0;
    var totalBytesCopied = 0;
    var totalFilesCopied = 0;
    var imageMinOptions = {
        progressive: true,
        interlaced: true,
        optimizationLevel: 3
    };

    return new Promise(function (resolve, reject) {
        glob(globSpec, function (error, files) {
            async.eachLimit(files, numberOfCPUs, function (file, next) {
                var destinationFile = path.join(destinationPath, file.substr(sourcePathLength));
                var imageMin = new ImageMin()
                    .src(file)
                    .dest(path.dirname(destinationFile))
                    .use(ImageMin.jpegtran(imageMinOptions))
                    .use(ImageMin.optipng(imageMinOptions))
                    .use(ImageMin.gifsicle(imageMinOptions));
                if (imageMinOptions.use) {
                    imageMinOptions.use.forEach(imageMin.use.bind(imageMin));
                }
                if (path.basename(file) !== path.basename(destinationFile)) {
                    imageMin.use(rename(path.basename(destinationFile)));
                }
                fsExtra.stat(file, function (error, fileStat) {
                    if (error) {
                        logError(file + " -- fstat error " + error.toString());
                        return next();
                    }
                    totalFilesCopied++;
                    imageMin.run(function (error, imageData) {
                        if (error) {
                            logError(file + " -- imagemin error " + error.toString());
                            return next();
                        }
                        var statusMessage;
                        var originalFileSize = fileStat.size;
                        var optimizedFileSize = imageData[0].contents.length;
                        var bytesSaved = originalFileSize - optimizedFileSize;
                        totalBytesConsidered += originalFileSize;
                        totalBytesCopied += optimizedFileSize;
                        if (bytesSaved > 9) {
                            statusMessage = "saved " + prettyBytes(bytesSaved) + " (" + ((optimizedFileSize / originalFileSize) * 100).toFixed() + "%)";
                        } else {
                            statusMessage = "is optimized";
                        }
                        logInfo("ᗘ " + file + " -- copy to " + destinationFile + " -- " + statusMessage);
                        process.nextTick(next);
                    });
                });
            }, function (error) {
                compressionStats.totalFiles += totalFilesCopied;
                compressionStats.totalBytesConsidered += totalBytesConsidered;
                compressionStats.totalBytesCompressed += totalBytesCopied;
                compressionStats.endTime = new Date();
                if (error) {
                    reject(new Error("optimizeImages process error " + error.toString()));
                } else {
                    var totalSaved = totalBytesConsidered - totalBytesCopied;
                    var percentSaved = totalBytesConsidered == 0 ? 0 : ((totalSaved / totalBytesConsidered) * 100).toFixed() + "%";
                    logInfo("ᙘ Completed image optimization for " + totalFilesCopied + " files, saved " + prettyBytes(totalSaved) + " " + percentSaved);
                    resolve();
                }
            });
        });
    });
}

/**
 * Copy images without optimizing them.
 * @param {string} sourcePath Path to the root folder under which to find images. Image files are jpg, jpeg, png, gif, svg.
 * @param {string} destinationPath Path where to copy optimized files.
 * @param {string} imagesGlobSpec Glob specification of files to match in source path.
 */
function copyImages(sourcePath, destinationPath, imagesGlobSpec) {
    logInfo("ᗘ Starting image copy");
    var globSpec = path.join(sourcePath, "/**/") + "*." + imagesGlobSpec;
    var sourcePathLength = sourcePath.length;
    var totalBytesConsidered = 0;
    var totalBytesCopied = 0;
    var totalFilesCopied = 0;

    return new Promise(function (resolve, reject) {
        glob(globSpec, function (error, files) {
            async.eachLimit(files, numberOfCPUs, function (file, next) {
                var destinationFile = path.join(destinationPath, file.substr(sourcePathLength));
                fsExtra.stat(file, function (error, fileStat) {
                    var newPath = path.dirname(destinationFile);
                    var originalFileSize;
                    if (error) {
                        logError(file + " -- fstat error " + error.toString());
                        return next();
                    }
                    if (!fsExtra.existsSync(newPath)) {
                        fsExtra.mkdirSync(newPath);
                    }
                    fsExtra.copyFileSync(file, destinationFile);
                    totalFilesCopied++;
                    originalFileSize = fileStat.size;
                    totalBytesConsidered += originalFileSize;
                    totalBytesCopied += originalFileSize;
                    logInfo("ᗘ " + file + " -- copy to " + destinationFile);
                    process.nextTick(next);
                });
            }, function (error) {
                compressionStats.totalFiles += totalFilesCopied;
                compressionStats.totalBytesConsidered += totalBytesConsidered;
                compressionStats.totalBytesCompressed += totalBytesCopied;
                compressionStats.endTime = new Date();
                if (error) {
                    reject(new Error("copyImages process error " + error.toString()));
                } else {
                    logInfo("ᙘ Completed image copy for " + totalFilesCopied + " files, total " + prettyBytes(totalBytesCopied));
                    resolve();
                }
            });
        });
    });
}

/**
 * Optimize all js files found in configuration.pageManifest and copy the optimized
 * version to configuration.destinationFolder.
 * 
 * @param {object} varynConfiguration Configuration properties.
 */
function optimizeJS(varynConfiguration) {
    logInfo("ᗘ Starting JavaScript optimization for Varyn app files");
    let sourceFolder = varynConfiguration.source;
    let destinationFolder = varynConfiguration.destination;
    const fileGroups = varynConfiguration.pageManifest;
    var totalBytesConsidered = 0;
    var totalBytesCopied = 0;
    var minifyJSCode = {};
    var minifyJSOptions = {
        warnings: true,
        toplevel: false,
        compress: {},
        mangle: {}
    };

    function prepareJSFile(file) {
        const fileName = path.basename(file);
        const filePath = path.join(sourceFolder, file);
        var fileSize;
        var fileContents;
        logInfo("ᗘ JS compress source " + filePath);

        if (configuration.jsFilesToIgnore.indexOf(fileName) < 0) {
            fileContents = fsExtra.readFileSync(filePath, { encoding: "utf8", flag: "r" });
            if (fileContents != null && fileContents.length > 0) {
                compressionStats.totalFiles ++;
                fileSize = Buffer.byteLength(fileContents);
                totalBytesConsidered += fileSize;
                compressionStats.totalBytesConsidered += fileSize;
                minifyJSCode[fileName] = fileContents;
            } else {
                logError("prepareJSFile Error reading file " + filePath);
            }
        }
    }

    async function completeJSCompression(packageName) {
        const destinationFile = path.join(destinationFolder, packageName);
        logInfo("ᗘ JS compress save as " + destinationFile);
        try {
            const compressedJSCode = await minify(minifyJSCode, minifyJSOptions);
            if (compressedJSCode != null && compressedJSCode.code !== null) {
                if (!fsExtra.existsSync(destinationFolder)) {
                    fsExtra.mkdirSync(destinationFolder);
                }
                fsExtra.writeFileSync(destinationFile, compressedJSCode.code);
                totalBytesCopied = Buffer.byteLength(compressedJSCode.code);
                compressionStats.totalBytesCompressed += totalBytesCopied;
                const bytesSaved = totalBytesConsidered - totalBytesCopied;
                let statusMessage;
                if (bytesSaved > 9) {
                    statusMessage = "JS compression saved " + prettyBytes(bytesSaved) + " (" + ((bytesSaved / totalBytesConsidered) * 100).toFixed() + "%)";
                } else {
                    statusMessage = "JS is optimized";
                }
                logInfo("ᗘ JS compressed to " + destinationFile + " -- " + statusMessage);
            } else {
                logError("completeJSCompression something wrong with Terser " + compressedJSCode.error);
            }
        } catch (compressError) {
            logError("completeJSCompression Compress error " + compressError.toString());
        }
    }

    return new Promise(function (resolve, reject) {
        for (const fileGroup in fileGroups) {
            const fileList = fileGroups[fileGroup];
            const fileParts = path.parse(fileList[fileList.length - 1]);
            const packageName = fileParts.name + ".min.js";
            for (let index = 0; index < fileList.length; index ++) {
                prepareJSFile(fileList[index]);
            }
            completeJSCompression(packageName);
        }
        return resolve();
    });
}

/**
 * Create the compressed libraries.
 * 
 * @param {object} varynConfiguration Configuration properties.
 */
async function optimizeJSLibs(varynConfiguration) {
    logInfo("ᗘ Starting JavaScript optimization for libraries");
    let totalBytesConsidered = 0;
    let minifyJSCode = {};
    const sourcePath = varynConfiguration.source;
    const destinationPath = varynConfiguration.destination;
    const minifyJSOptions = {
        warnings: true,
        toplevel: false,
        compress: {},
        mangle: {}
    };

    return new Promise(function (resolve, reject) {
        fsExtra.ensureDir(destinationPath)
            .then(function () {
                async.map(varynConfiguration.libsToCopy, function (file) {
                    var fileName = path.join(sourcePath, file);
                    fsExtra.stat(fileName, function (error, fileStat) {
                        if (error) {
                            throw (new Error("optimizeJSLibs fstat error -- " + fileName + ":  " + error.toString()));
                        } else {
                            var destinationFile = path.join(destinationPath, file);
                            var originalFileSize = fileStat.size;
                            fsExtra.copyFileSync(fileName, destinationFile);
                            logInfo("ᗘ " + fileName + " -- copied to " + destinationFile);
                            compressionStats.totalFiles++;
                            compressionStats.totalBytesConsidered += originalFileSize;
                            compressionStats.totalBytesCompressed += originalFileSize;
                        }
                    });
                }, function (error, result) {
                    if (error != null) {
                        return reject(error);
                    } else {
                        return result;
                    }
                });
            })
            .then(function (result) {
                var destinationFile = path.join(destinationPath, varynConfiguration.combinedLibFileName);
                varynConfiguration.libsToCombine.forEach(function (file) {
                    var fileName = path.join(sourcePath, file);
                    var fileSize;
                    var fileContents = fsExtra.readFileSync(fileName, { encoding: "utf8", flag: "r" });
                    if (fileContents != null && fileContents.length > 0) {
                        compressionStats.totalFiles++;
                        fileSize = Buffer.byteLength(fileContents);
                        compressionStats.totalBytesConsidered += fileSize;
                        totalBytesConsidered += fileSize;
                        minifyJSCode[file] = fileContents;
                    }
                });
                minify(minifyJSCode, minifyJSOptions)
                .then(function(compressedJSCode) {
                    fsExtra.writeFileSync(destinationFile, compressedJSCode.code);
                    var totalBytesCopied = Buffer.byteLength(compressedJSCode.code);
                    compressionStats.totalBytesCompressed += totalBytesCopied;
                    var bytesSaved = totalBytesConsidered - totalBytesCopied;
                    var statusMessage;
                    if (bytesSaved > 9) {
                        statusMessage = "JS compression saved " + prettyBytes(bytesSaved) + "(" + ((bytesSaved / totalBytesConsidered) * 100).toFixed() + "%)";
                    } else {
                        statusMessage = "JS is optimized";
                    }
                    logInfo("ᗘ Lib JS compressed to " + destinationFile + " -- " + statusMessage);
                    return resolve(result);    
                }, function(error) {
                    logError("Minify error " + error.toString());
                })
                .catch(function(exception) {
                    logError("Minify exception " + exception.toString());
                });
            })
            .catch(function(exception) {
                logError("fsExtra.ensureDir exception " + exception.toString());
            });
    });
}

/**
 * Determine how to handle images: either optimize and copy or just copy. Returns a Promise.
 * @param {string} sourcePath Path to folder of image files.
 * @param {string} destinationPath Path to folder to copy source images to.
 * @param {string} imagesGlobSpec Glob specification of images to match in source path.
 * @returns Promise Resolves when images are copied.
 */
function handleImages(sourcePath, destinationPath, imagesGlobSpec) {
    if (configuration.optimizeImages) {
        return optimizeImages(sourcePath, destinationPath, imagesGlobSpec);
    } else {
        return copyImages(sourcePath, destinationPath, imagesGlobSpec);
    }
}

/**
 * Display end of build statistics.
 */
function showStats() {
    if (compressionStats.endTime != null) {
        logInfo("ᙘ Build stats:");
        var dateDiff = (compressionStats.endTime.getTime() - compressionStats.startTime.getTime()) / 1000;
        var bytesSaved = compressionStats.totalBytesConsidered - compressionStats.totalBytesCompressed;
        var bytesRatio = ((bytesSaved / compressionStats.totalBytesConsidered) * 100).toFixed();
        logInfo("ᙘ Completed build in " + dateDiff + "s: " + compressionStats.totalFiles + " files, originally " + prettyBytes(compressionStats.totalBytesConsidered) + ", now " + prettyBytes(compressionStats.totalBytesCompressed) + " saving " + prettyBytes(bytesSaved) + " (" + bytesRatio + "%).");
    }
}

/**
 * Run the build:
 *   - we can run in parallel css, js, html, and image optimizations
 *   - when those tasks are complete then run the copy files to distrib
 *   - when distrib is complete then run copy files to folder
 *   - after everything is done then show build statistics.
 */
function runBuild() {
    logInfo("Running build now.");
    process.exit(0);

    Promise.all([
        optimizeJS(configuration),
        optimizeJSLibs(configuration),
        handleImages(configuration.source, configuration.destination, configuration.imagesFileSpec)
    ]).then(function (result) {
        logInfo("ᙘ All builds complete");
        showStats(result);
    }).catch(function (error) {
        logError(error.toString() + " -- probably unhandled error.");
    });
}

updateConfiguration()
.then(function() {
    runBuild();
});
