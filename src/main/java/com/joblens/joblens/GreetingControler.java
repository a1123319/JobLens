package com.joblens.joblens;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

@Controller
public class GreetingControler {
	
	@GetMapping("/greeting")
	public String greeting(@RequestParam(name="yourName", required=false, defaultValue="World") String yourName, Model model) {
		model.addAttribute("name", yourName);
		return "greeting.html";
	}
}
