package com.joblens.joblens;

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
	public CommandLineRunner demo(PeopleRepository repository) {
		return (args) -> {
			// save a few Persons
			repository.save(new People("Jack", "Bauer"));
			repository.save(new People("Chloe", "O'Brian"));
			repository.save(new People("Kim", "Bauer"));
			repository.save(new People("David", "Palmer"));
			repository.save(new People("Michelle", "Dessler"));

			// fetch all Persons
			logger.info("Persons found with findAll():");
			logger.info("-------------------------------");
			repository.findAll().forEach(Person -> {
				logger.info(Person.toString());
			});
			logger.info("");

			// fetch an individual Person by ID
			// People Person = repository.findById(1);
			// logger.info("Person found with findById(1):");
			// logger.info("--------------------------------");
			// logger.info(Person.toString());
			// logger.info("");

			// // fetch Persons by last name
			// logger.info("Person found with findByLastName('Bauer'):");
			// logger.info("--------------------------------------------");
			// repository.findByLastName("Bauer").forEach(bauer -> {
			// 	logger.info(bauer.toString());
			// });
			// logger.info("");
		};
	}

}