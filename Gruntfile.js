module.exports = function (grunt) {
    grunt.initConfig({
        ts: {
            options: {
                rootDir: "Resources/Private/Typescript",
                module: "amd"
            },
            default: {
                watch: "Resources/Private/Typescript",
                src: ["**/*.ts", "!node_modules/**", "!Configuration/**"],
                outDir: "Resources/Public/JavaScript",
                tsconfig: true
            }
        }
    });
    grunt.loadNpmTasks("grunt-ts");
    grunt.registerTask("default", ["ts"]);
};