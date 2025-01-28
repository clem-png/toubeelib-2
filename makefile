.PHONY: build

# Fonction pour vérifier et supprimer les dossiers et fichiers
check_and_install = \
    if [ -d "$1/vendor" ]; then \
        rm -rf "$1/vendor"; \
    fi; \
    if [ -f "$1/composer.lock" ]; then \
        rm "$1/composer.lock"; \
    fi; \
    composer install -d "$1"

build:
	@echo "Construction dans ./api-auth/app..."
	$(call check_and_install,./api-auth/app)
	@echo "Construction dans ./api-praticiens/app..."
	$(call check_and_install,./api-praticiens/app)
	@echo "Construction dans ./api-rdv/app..."
	$(call check_and_install,./api-rdv/app)
	@echo "Construction dans ./api-patient/app..."
	$(call check_and_install,./api-patient/app)
	@echo "Construction dans ./gateway..."
	$(call check_and_install,./gateway)
