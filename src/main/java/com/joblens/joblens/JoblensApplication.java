package com.joblens.joblens;

import com.joblens.joblens.entity.Company;
import com.joblens.joblens.repository.CompanyRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.boot.CommandLineRunner;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.Bean;

@SpringBootApplication
public class JoblensApplication {

	private static final Logger logger = LoggerFactory.getLogger(JoblensApplication.class);

	public static void main(String[] args) {
		SpringApplication.run(JoblensApplication.class, args);
	}

	@Bean
	public CommandLineRunner demo(CompanyRepository companyRepository) {
		return (args) -> {
			// Create a new random company
			Company newCompany = new Company();
			newCompany.setId((int) (Math.random() * 100000)); // Generate a random ID for demonstration
			newCompany.setName("Random Company " + (int) (Math.random() * 1000));
			newCompany.setCategory("Random Category " + (int) (Math.random() * 10));
			newCompany.setSector("Random Sector " + (int) (Math.random() * 5));

			companyRepository.save(newCompany);
			logger.info("Created and saved a new random company: " + newCompany.getName());

			// Fetch all companies
			logger.info("Companies found with findAll():");
			logger.info("-------------------------------");
			companyRepository.findAll().forEach(company -> {
				logger.info("Company[id=" + company.getId() + ", name='" + company.getName() + "']");
			});
			logger.info("");
		};
	}
}