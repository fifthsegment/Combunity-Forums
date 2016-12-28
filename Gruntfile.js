module.exports = function(grunt) {
	grunt.initConfig({
		uglify: {
		    my_target: {
		      files: {
		        './combunity-forums/public/js/combunity-ashes-public.min.js': ['./combunity-forums/public/js/combunity-ashes-public.js']
		      }
		    }
		},
    	watch: {
    		files: [
	      		'./combunity-forums/public/js/combunity-ashes-public.js'
	    	],
    		tasks: ['uglify']
    	},
    	compress: {
		    build: {
		        options: {
		            archive: './dist/combunity-forums-' + grunt.template.today('yyyy-mm-dd') + '.zip',
		            mode: 'zip'
		        },
		        files: [
		            { src : ['./combunity-forums/**'] }
		        ]
		    }
		},
		wp_deploy: {
	        deploy: { 
	            options: {
	                plugin_slug: 'combunity-forums',
	                plugin_main_file: 'combunity-ashes.php',
	                svn_user: 'fifthsegment',  
	                build_dir: 'combunity-forums', //relative path to your build directory
	                assets_dir: 'wp-assets' //relative path to your assets directory (optional).
	            },
	        }
	    },
	});
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-wp-deploy');
	grunt.registerTask('uglifyup', ['uglify'] );
	grunt.registerTask('default', ['watch'] );
	grunt.registerTask('zipup', ['compress'] );
	grunt.registerTask('wpdeploy', ['wp_deploy'] );
};