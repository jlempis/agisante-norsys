# ==============================================================================
#                                CONFIGURATION
# ==============================================================================

# ------------------------------------------------------------------------------
# 1. Configuation des serveurs
# ------------------------------------------------------------------------------
set :stages,        %w(recette prod)
set :default_stage, "recette"
set :stage_dir,     "app/config/deploy"

set :application,   "gestime"
set :deploy_to,     "/var/www/gestime.re7"

require 'capistrano/ext/multistage'

# ------------------------------------------------------------------------------
# 2. Configuration de la sécurité
# ------------------------------------------------------------------------------
# Security : specification of the specific ssh key of the server
# ssh_options[:keys] = %w(/Users/johndoe/.ssh/johndoe.pem)
# ssh_options[:keys] = [File.join(ENV["HOME"], ".ssh", "id_rsa")]
# ssh_options[:forward_agent] = true

# ------------------------------------------------------------------------------
# 3. Configuration gestion de configuration
# ------------------------------------------------------------------------------
# Repository path
set :repository,        "ssh://git@github.com/jlempis/gestime.git"
# SCM type
set :scm,               :git
set :branch,            "master"
# Méthode de déploiement
set :deploy_via,        :copy
# Les deux prochaines lignes sont nécéssaires si on deploie sur la meme machine
set :copy_dir, "/home/jladm/tmp"
set :remote_copy_dir, "/tmp"

# ------------------------------------------------------------------------------
# 4. Configuration du projet
# ------------------------------------------------------------------------------
# Fichiers à partager entre les environnements : images, config..
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,   [app_path + "/logs",  "vendor"]
set :model_manager,     "doctrine"
set :use_composer,      true
set :update_vendors,    false

set :writable_dirs, ["app/cache", "app/logs"]
set :webserver_user, "www-data"
set :permission_method, :chown
set :use_set_permissions, true
set :dump_assetic_assets, true

set :parameters_dir, "app/config/parameters"
set :parameters_file, false

# ------------------------------------------------------------------------------
# 5. Parametres Capifony
# ------------------------------------------------------------------------------
set :user,          "root"
set :use_sudo,      true
set :app_path,      "app"
set  :keep_releases,  3

before "deploy:share_childs", "upload_parameters"
after "deploy:update" do
  run "chmod -R 777 #{deploy_to}/current/app/cache #{deploy_to}/current/app/cache"
end
# ==============================================================================
#                                     TACHES
# ==============================================================================
# ------------------------------------------------------------------------------
# Parametres specifiques à chaque environnement
# ------------------------------------------------------------------------------
task :upload_parameters, :except => { :parameters_file => nil } do



capifony_pretty_print "-->debut tache <-----------------------"


  servers = find_servers_for_task(current_task)
  servers.each do |server|
    capifony_pretty_print "-->suite tache <-----------------------"
    parameters_file = server.options[:parameters_file]

    origin_file = parameters_dir + "/" + parameters_file if parameters_dir && parameters_file
    capifony_pretty_print "-->original file <-----------------------"
    capifony_pretty_print origin_file
    if origin_file && File.exists?(origin_file)
        capifony_pretty_print "-->original file exist<-----------------------"
      ext = File.extname(parameters_file)
      relative_path = "app/config/parameters" + ext

      if shared_files && shared_files.include?(relative_path)
        destination_file = shared_path + "/" + relative_path
      else
        destination_file = latest_release + "/" + relative_path
      end

      run "#{try_sudo} mkdir -p #{File.dirname(destination_file)}", :hosts => server

      capifony_pretty_print "--> Uploading " + parameters_file + " to " + server.host
      top.upload(origin_file, destination_file, { :hosts => server })
      capifony_puts_ok
    end
  end
end
# ------------------------------------------------------------------------------
# Effacer le cache de la production
# ------------------------------------------------------------------------------
task :cc do
    run "rm -rf #{deploy_to}/current/app/cache"
end

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL